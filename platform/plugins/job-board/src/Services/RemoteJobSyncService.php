<?php

namespace Botble\JobBoard\Services;

use Botble\ACL\Models\User;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\JobBoard\Enums\JobStatusEnum;
use Botble\JobBoard\Enums\ModerationStatusEnum;
use Botble\JobBoard\Enums\SalaryRangeEnum;
use Botble\JobBoard\Models\Category;
use Botble\JobBoard\Models\Company;
use Botble\JobBoard\Models\Job;
use Botble\JobBoard\Models\JobType;
use Botble\JobBoard\Models\Tag;
use Botble\JobBoard\Supports\JobImport\RemoteJobRecord;
use Botble\JobBoard\Supports\JobImport\RemoteJobSourceFactory;
use Botble\Location\Models\Country;
use Botble\Location\Models\State;
use Botble\Slug\Facades\SlugHelper;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RemoteJobSyncService
{
    public function __construct(
        protected RemoteJobSourceFactory $sourceFactory,
        protected RemoteJobSeoService $seoService
    ) {
    }

    public function sync(?string $sourceKey = null, ?bool $deactivateMissing = null): array
    {
        $sources = config('plugins.job-board.sources.sources', []);

        if ($sourceKey) {
            $sources = Arr::only($sources, [$sourceKey]);
        }

        $results = [];

        foreach ($sources as $key => $config) {
            $source = $this->sourceFactory->make($key, $config);

            if (! $source->enabled()) {
                continue;
            }

            $results[$key] = $this->syncSource($source, $config, $deactivateMissing);
        }

        return $results;
    }

    protected function syncSource($source, array $config, ?bool $deactivateMissing = null): array
    {
        $records = $source->fetch();
        $syncedJobIds = [];
        $created = 0;
        $updated = 0;

        foreach ($records as $record) {
            DB::transaction(function () use ($source, $config, $record, &$syncedJobIds, &$created, &$updated): void {
                $company = $this->upsertCompany($source->key(), $record);
                $job = $this->findJob($source->key(), $record);
                $isNew = ! $job->exists;

                $job->forceFill($this->buildJobData($source->key(), $config, $record, $company));
                $job->imported_at = $job->imported_at ?: Carbon::now();

                if (! $job->author_id) {
                    $author = $this->resolveAuthor();

                    if ($author) {
                        $job->author()->associate($author);
                    }
                }

                $job->save();

                if ($isNew) {
                    SlugHelper::createSlug($job);
                    $created++;
                } else {
                    $updated++;
                }

                $job->categories()->sync($this->resolveCategories($record->categories));
                $job->jobTypes()->sync($this->resolveTypes($record->types));
                $job->tags()->sync($this->resolveTags($record->tags, $record->isRemote));
                $this->seoService->save($job, $record);

                $syncedJobIds[] = $job->getKey();
            });
        }

        $deactivate = $deactivateMissing ?? (bool) config('plugins.job-board.sources.deactivate_missing_jobs', true);
        $allowEmptySync = (bool) Arr::get($config, 'allow_empty_sync', false);
        $closed = $deactivate && ($syncedJobIds !== [] || $allowEmptySync)
            ? $this->deactivateMissingJobs($source->key(), $syncedJobIds)
            : 0;

        return [
            'label' => $source->label(),
            'fetched' => count($records),
            'created' => $created,
            'updated' => $updated,
            'closed' => $closed,
        ];
    }

    protected function findJob(string $sourceKey, RemoteJobRecord $record): Job
    {
        return Job::query()
            ->where('source_name', $sourceKey)
            ->where('source_job_id', $record->externalId)
            ->first() ?: new Job();
    }

    protected function upsertCompany(string $sourceKey, RemoteJobRecord $record): ?Company
    {
        $companyName = Arr::get($record->company, 'name');

        if (! $companyName) {
            return null;
        }

        $query = Company::query();
        $sourceCompanyId = Arr::get($record->company, 'external_id');

        if ($sourceCompanyId) {
            $query->where('source_name', $sourceKey)->where('source_company_id', $sourceCompanyId);
        } else {
            $query->where('name', $companyName);

            if ($website = Arr::get($record->company, 'website')) {
                $query->orWhere('website', $website);
            }
        }

        $company = $query->first() ?: new Company();

        $company->forceFill([
            'name' => $companyName,
            'email' => Arr::get($record->company, 'email'),
            'phone' => Arr::get($record->company, 'phone'),
            'website' => Arr::get($record->company, 'website'),
            'logo' => Arr::get($record->company, 'logo'),
            'description' => Arr::get($record->company, 'description') ?: Str::limit($record->description, 250, ''),
            'content' => Arr::get($record->company, 'description'),
            'address' => $record->address ?: $record->location,
            'status' => BaseStatusEnum::PUBLISHED,
            'source_name' => $sourceKey,
            'source_company_id' => $sourceCompanyId,
            'imported_at' => $company->imported_at ?: Carbon::now(),
            'last_synced_at' => Carbon::now(),
            'country_id' => $this->resolveCountryId($record),
            'state_id' => $this->resolveStateId($record),
        ]);

        $company->save();

        if (! $company->slugable) {
            SlugHelper::createSlug($company);
        }

        return $company;
    }

    protected function buildJobData(string $sourceKey, array $config, RemoteJobRecord $record, ?Company $company): array
    {
        $now = Carbon::now();
        $expireDays = max(1, (int) config('plugins.job-board.sources.default_expire_days', 30));
        $expireDate = $record->closingAt
            ?: $record->expiresAt
            ?: $record->publishedAt?->copy()->addDays($expireDays)
            ?: $now->copy()->addDays($expireDays);

        $description = $record->description ?: $this->seoService->buildDescription($record);
        $content = $this->seoService->buildContent($record);

        return [
            'name' => $record->title,
            'description' => $description,
            'content' => $content,
            'company_id' => $company?->getKey(),
            'address' => $record->address ?: $record->location,
            'status' => JobStatusEnum::PUBLISHED,
            'apply_url' => $record->applyUrl ?: $record->url,
            'external_apply_behavior' => 'new_tab',
            'salary_from' => $record->salaryFrom,
            'salary_to' => $record->salaryTo,
            'salary_range' => $this->normalizeSalaryRange($record->salaryRange),
            'hide_salary' => ! $record->salaryFrom && ! $record->salaryTo,
            'number_of_positions' => 1,
            'expire_date' => $expireDate,
            'never_expired' => false,
            'hide_company' => false,
            'auto_renew' => false,
            'is_featured' => false,
            'country_id' => $this->resolveCountryId($record),
            'state_id' => $this->resolveStateId($record),
            'city_id' => null,
            'is_remote' => (bool) $record->isRemote,
            'start_date' => $record->publishedAt?->toDateString(),
            'application_closing_date' => $record->closingAt,
            'moderation_status' => ModerationStatusEnum::APPROVED,
            'source_name' => $sourceKey,
            'source_type' => Arr::get($config, 'driver'),
            'source_job_id' => $record->externalId,
            'source_url' => $record->url,
            'source_payload_hash' => sha1(json_encode($record->metadata) . $record->title . $description),
            'last_synced_at' => $now,
            'source_updated_at' => $record->sourceUpdatedAt,
        ];
    }

    protected function resolveAuthor(): ?User
    {
        $email = config('plugins.job-board.sources.default_author_email');

        if ($email) {
            return User::query()->where('email', $email)->first();
        }

        return User::query()->orderBy('id')->first();
    }

    protected function resolveCategories(array $categories): array
    {
        return collect($categories)
            ->filter()
            ->map(function (string $name) {
                $category = Category::query()->firstOrCreate([
                    'name' => trim($name),
                ], [
                    'status' => BaseStatusEnum::PUBLISHED,
                ]);

                if (! $category->slugable) {
                    SlugHelper::createSlug($category);
                }

                return $category->getKey();
            })
            ->filter()
            ->values()
            ->all();
    }

    protected function resolveTypes(array $types): array
    {
        return collect($types)
            ->filter()
            ->map(function (string $name) {
                $type = JobType::query()->firstOrCreate([
                    'name' => trim($name),
                ], [
                    'status' => BaseStatusEnum::PUBLISHED,
                ]);

                return $type->getKey();
            })
            ->filter()
            ->values()
            ->all();
    }

    protected function resolveTags(array $tags, bool $isRemote): array
    {
        if ($isRemote) {
            $tags[] = 'Remote';
        }

        return collect($tags)
            ->filter()
            ->map(function (string $name) {
                $tag = Tag::query()->firstOrCreate([
                    'name' => trim($name),
                ], [
                    'status' => BaseStatusEnum::PUBLISHED,
                ]);

                if (! $tag->slugable) {
                    SlugHelper::createSlug($tag);
                }

                return $tag->getKey();
            })
            ->filter()
            ->values()
            ->all();
    }

    protected function deactivateMissingJobs(string $sourceKey, array $syncedJobIds): int
    {
        $query = Job::query()->where('source_name', $sourceKey);

        if ($syncedJobIds !== []) {
            $query->whereNotIn('id', $syncedJobIds);
        }

        return $query->update([
            'status' => JobStatusEnum::CLOSED,
            'expire_date' => Carbon::now(),
        ]);
    }

    protected function resolveCountryId(RemoteJobRecord $record): ?int
    {
        $country = $record->country ?: config('plugins.job-board.sources.default_country');

        if (! $country || ! is_plugin_active('location')) {
            return null;
        }

        return Country::query()
            ->where(function ($query) use ($country): void {
                $query->where('name', $country)
                    ->orWhere('code', strtoupper($country));
            })
            ->value('id');
    }

    protected function resolveStateId(RemoteJobRecord $record): ?int
    {
        if (! $record->state || ! is_plugin_active('location')) {
            return null;
        }

        $query = State::query()->where('name', $record->state);

        if ($countryId = $this->resolveCountryId($record)) {
            $query->where('country_id', $countryId);
        }

        return $query->value('id');
    }

    protected function normalizeSalaryRange(?string $salaryRange): SalaryRangeEnum|string
    {
        $salaryRange = Str::lower((string) $salaryRange);

        return match (true) {
            Str::contains($salaryRange, 'hour') => SalaryRangeEnum::HOURLY,
            Str::contains($salaryRange, 'day') => SalaryRangeEnum::DAILY,
            Str::contains($salaryRange, 'week') => SalaryRangeEnum::WEEKLY,
            Str::contains($salaryRange, 'year'), Str::contains($salaryRange, 'annual') => SalaryRangeEnum::YEARLY,
            default => SalaryRangeEnum::MONTHLY,
        };
    }
}
