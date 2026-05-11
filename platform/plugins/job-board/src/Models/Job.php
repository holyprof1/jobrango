<?php

namespace Botble\JobBoard\Models;

use Botble\Base\Casts\SafeContent;
use Botble\Base\Models\BaseModel;
use Botble\Base\Models\BaseQueryBuilder;
use Botble\JobBoard\Enums\JobStatusEnum;
use Botble\JobBoard\Enums\ModerationStatusEnum;
use Botble\JobBoard\Enums\SalaryRangeEnum;
use Botble\JobBoard\Enums\SalaryTypeEnum;
use Botble\JobBoard\Facades\JobBoardHelper;
use Botble\JobBoard\Models\Builders\FilterJobsBuilder;
use Botble\JobBoard\Models\Concerns\UniqueId;
use Botble\Media\Facades\RvMedia;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\DB;

class Job extends BaseModel
{
    use UniqueId;

    protected $table = 'jb_jobs';

    protected $fillable = [
        'name',
        'description',
        'content',
        'company_id',
        'address',
        'status',
        'apply_url',
        'external_apply_behavior',
        'is_freelance',
        'career_level_id',
        'salary_from',
        'salary_to',
        'salary_range',
        'salary_type',
        'currency_id',
        'degree_level_id',
        'job_shift_id',
        'job_experience_id',
        'functional_area_id',
        'hide_salary',
        'number_of_positions',
        'expire_date',
        'author_id',
        'author_type',
        'views',
        'number_of_applied',
        'hide_company',
        'latitude',
        'longitude',
        'auto_renew',
        'is_featured',
        'external_apply_clicks',
        'country_id',
        'state_id',
        'city_id',
        'employer_colleagues',
        'start_date',
        'application_closing_date',
        'is_remote',
        'zip_code',
        'unique_id',
        'application_mode',
        'application_form_schema',
        'application_form_settings',
        'source_name',
        'source_type',
        'source_job_id',
        'source_url',
        'source_payload_hash',
        'imported_at',
        'last_synced_at',
        'source_updated_at',
    ];

    protected $casts = [
        'status' => JobStatusEnum::class,
        'moderation_status' => ModerationStatusEnum::class,
        'salary_range' => SalaryRangeEnum::class,
        'salary_type' => SalaryTypeEnum::class,
        'expire_date' => 'datetime',
        'start_date' => 'date',
        'application_closing_date' => 'datetime',
        'is_remote' => 'boolean',
        'application_form_schema' => 'array',
        'application_form_settings' => 'array',
        'imported_at' => 'datetime',
        'last_synced_at' => 'datetime',
        'source_updated_at' => 'datetime',
        'name' => SafeContent::class,
        'description' => SafeContent::class,
        'content' => SafeContent::class,
        'address' => SafeContent::class,
        'apply_url' => SafeContent::class,
    ];

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(JobSkill::class, 'jb_jobs_skills', 'job_id', 'job_skill_id');
    }

    public function careerLevel(): BelongsTo
    {
        return $this->belongsTo(CareerLevel::class, 'career_level_id')->withDefault();
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class)->withDefault();
    }

    public function degreeLevel(): BelongsTo
    {
        return $this->belongsTo(DegreeLevel::class, 'degree_level_id')->withDefault();
    }

    public function jobShift(): BelongsTo
    {
        return $this->belongsTo(JobShift::class, 'job_shift_id')->withDefault();
    }

    public function jobExperience(): BelongsTo
    {
        return $this->belongsTo(JobExperience::class, 'job_experience_id')->withDefault();
    }

    public function functionalArea(): BelongsTo
    {
        return $this->belongsTo(FunctionalArea::class, 'functional_area_id')->withDefault();
    }

    public function jobTypes(): BelongsToMany
    {
        return $this->belongsToMany(JobType::class, 'jb_jobs_types', 'job_id', 'job_type_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id')->withDefault();
    }

    public function author(): MorphTo
    {
        return $this->morphTo()->withDefault();
    }

    protected function salaryText(): Attribute
    {
        return Attribute::make(
            get: function ($_, array $attributes = []) {
                if ($attributes['hide_salary']) {
                    return trans('plugins/job-board::messages.attractive');
                }

                $salaryType = $attributes['salary_type'] ?? SalaryTypeEnum::FIXED;

                // Handle different salary types
                switch ($salaryType) {
                    case SalaryTypeEnum::NEGOTIABLE:
                        return trans('plugins/job-board::messages.negotiable');
                    case SalaryTypeEnum::COMPETITIVE:
                        return trans('plugins/job-board::messages.competitive');
                    case SalaryTypeEnum::HIDDEN:
                        return trans('plugins/job-board::messages.attractive');
                    case SalaryTypeEnum::FIXED:
                    default:
                        $salaryRange = $this->displaySalaryRangeLabel();
                        $from = (float) $attributes['salary_from'];
                        $to = (float) $attributes['salary_to'];

                        if ($from || $to) {
                            if ($from && $to) {
                                return sprintf(
                                    '%s - %s / %s',
                                    $this->formatDisplayedSalaryAmount($from),
                                    $this->formatDisplayedSalaryAmount($to),
                                    $salaryRange
                                );
                            }

                            if ($from) {
                                return sprintf(
                                    '%s / %s',
                                    __('From :price', ['price' => $this->formatDisplayedSalaryAmount($from)]),
                                    $salaryRange
                                );
                            }

                            return sprintf(
                                '%s / %s',
                                __('Upto :price', ['price' => $this->formatDisplayedSalaryAmount($to)]),
                                $salaryRange
                            );
                        }

                        return trans('plugins/job-board::messages.attractive');
                }
            }
        );
    }

    protected function listingSalaryText(): Attribute
    {
        return Attribute::get(function () {
            if ($this->hide_salary) {
                return trans('plugins/job-board::messages.attractive');
            }

            if ($this->isNegotiableSalary()) {
                return trans('plugins/job-board::messages.negotiable');
            }

            return $this->salary_text;
        });
    }

    protected function detailSalaryText(): Attribute
    {
        return Attribute::get(function () {
            if ($this->hide_salary) {
                return trans('plugins/job-board::messages.attractive');
            }

            if ($this->hasSalaryRange()) {
                return $this->buildFixedSalaryText();
            }

            return $this->salary_text;
        });
    }

    protected function salaryContextLabel(): Attribute
    {
        return Attribute::get(function () {
            if ($this->hide_salary || ! $this->isNegotiableSalary()) {
                return null;
            }

            return $this->hasSalaryRange()
                ? __('Negotiable range')
                : trans('plugins/job-board::messages.negotiable');
        });
    }

    protected function displayLocation(): Attribute
    {
        return Attribute::get(function () {
            $fullAddress = trim((string) ($this->full_address ?: ''));

            if ($fullAddress !== '') {
                return $fullAddress;
            }

            $location = trim((string) $this->location);

            if ($location !== '') {
                return $location;
            }

            return $this->is_remote ? __('Remote') : '';
        });
    }

    protected function publicDescription(): Attribute
    {
        return Attribute::get(function () {
            $description = trim((string) $this->description);

            if ($description !== '') {
                return $description;
            }

            $content = trim(strip_tags((string) $this->content));

            if ($content !== '') {
                return $content;
            }

            $category = $this->relationLoaded('categories')
                ? $this->categories->pluck('name')->filter()->first()
                : $this->categories()->pluck('name')->filter()->first();

            $companyName = trim((string) ($this->company?->name ?: __('Hiring team')));

            return __(':title at :company. Details will be updated soon.', [
                'title' => $this->name,
                'company' => $category ? $companyName . ' - ' . $category : $companyName,
            ]);
        });
    }

    protected function displayId(): Attribute
    {
        return Attribute::get(fn () => 'JR-JOB-' . $this->prefixedSequenceId());
    }

    public function displayCurrency(): Currency
    {
        $currency = $this->currency->getKey() ? $this->currency : get_application_currency();

        if ($currency && strtoupper((string) $currency->title) === 'USD') {
            return $currency;
        }

        return $this->fallbackNairaCurrency();
    }

    public function formatDisplayedSalaryAmount(float|int|string|null $amount): string
    {
        $amount = (float) $amount;
        $currency = $this->displayCurrency();

        if (strtoupper((string) $currency->title) === 'USD') {
            return format_price($amount, $currency, fullNumber: true);
        }

        return $this->nairaSymbol() . number_format(round($amount));
    }

    public function displaySalaryRangeLabel(): string
    {
        return match ($this->salary_range) {
            SalaryRangeEnum::HOURLY => __('Hour'),
            SalaryRangeEnum::DAILY => __('Day'),
            SalaryRangeEnum::WEEKLY => __('Week'),
            SalaryRangeEnum::MONTHLY => __('Month'),
            SalaryRangeEnum::YEARLY => __('Year'),
            default => $this->salary_range->label(),
        };
    }

    protected function fallbackNairaCurrency(): Currency
    {
        static $nairaCurrency = null;

        if ($nairaCurrency instanceof Currency) {
            return $nairaCurrency;
        }

        $nairaCurrency = Currency::query()->where('title', 'NGN')->first();

        if ($nairaCurrency instanceof Currency) {
            return $nairaCurrency;
        }

        $nairaCurrency = new Currency();
        $nairaCurrency->forceFill([
            'title' => 'NGN',
            'symbol' => '₦',
            'is_prefix_symbol' => true,
            'decimals' => 0,
            'number_format_style' => 'western',
            'exchange_rate' => 1,
        ]);

        return $nairaCurrency;
    }

    protected function nairaSymbol(): string
    {
        return html_entity_decode('&#8358;', ENT_QUOTES, 'UTF-8');
    }

    public function scopeNotExpired(Builder $query): Builder
    {
        $now = Carbon::now()->toDateTimeString();

        return $query->where(function ($query) use ($now): void {
            $query->where('jb_jobs.never_expired', true)
                ->orWhereNull('jb_jobs.expire_date')
                ->orWhere('jb_jobs.expire_date', '>=', $now);
        });
    }

    public function scopeNotClosed(Builder $query): Builder
    {
        return $query->where(function ($query): void {
            $query
                ->where('jb_jobs.status', '!=', JobStatusEnum::CLOSED)
                ->where('jb_jobs.application_closing_date', '>=', Carbon::now()->toDateTimeString())
                ->orWhere(function (Builder $query): void {
                    $query
                        ->where('jb_jobs.status', '!=', JobStatusEnum::CLOSED)
                        ->whereNull('jb_jobs.application_closing_date');
                });
        });
    }

    public function scopeExpired(Builder $query): Builder
    {
        return $query->where(function ($query): void {
            $query
                ->where('jb_jobs.expire_date', '<', Carbon::now()->toDateTimeString())
                ->where('jb_jobs.never_expired', false);
        });
    }

    public function scopeActive(Builder $query): Builder
    {
        // @phpstan-ignore-next-line
        return $query
            ->where(JobBoardHelper::getJobDisplayQueryConditions())
            ->notExpired()
            ->notClosed();
    }

    public function scopeAddSavedApplied(Builder $query): Builder
    {
        if (auth('account')->check()) {
            // @phpstan-ignore-next-line
            $query->addApplied()->addSaved();
        }

        return $query;
    }

    public function scopeAddApplied(Builder $query): Builder
    {
        if (! auth('account')->check() || auth('account')->user()->isEmployer()) {
            return $query;
        }

        $accountId = auth('account')->id();

        return $query
            ->leftJoin('jb_applications', function ($join) use ($accountId): void {
                $join
                    ->on('jb_applications.job_id', '=', 'jb_jobs.id')
                    ->where('jb_applications.account_id', $accountId);
            })
            ->addSelect(DB::raw('IF(jb_applications.job_id IS NULL, 0, jb_applications.job_id) AS is_applied'))
            ->addSelect('jb_jobs.*');
    }

    public function scopeAddSaved(Builder $query): Builder
    {
        if (! auth('account')->check() || auth('account')->user()->isEmployer()) {
            return $query;
        }

        $accountId = auth('account')->id();

        return $query
            ->leftJoin('jb_saved_jobs', function ($join) use ($accountId): void {
                $join
                    ->on('jb_saved_jobs.job_id', '=', 'jb_jobs.id')
                    ->where('jb_saved_jobs.account_id', $accountId);
            })
            ->addSelect(DB::raw('IF(jb_saved_jobs.job_id IS NULL, 0, jb_saved_jobs.job_id) AS is_saved'))
            ->addSelect('jb_jobs.*');
    }

    public function scopeByAccount(Builder $query, int $accountId): Builder
    {
        return $query->where(function (Builder $query) use ($accountId): void {
            $query->where([
                'jb_jobs.author_id' => $accountId,
                'jb_jobs.author_type' => Account::class,
            ])
                ->orWhereHas('company.accounts', function (Builder $query) use ($accountId): void {
                    $query->where('jb_companies_accounts.account_id', $accountId);
                });
        });
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'jb_jobs_categories', 'job_id', 'category_id');
    }

    public function savedJobs(): BelongsToMany
    {
        return $this->belongsToMany(Account::class, 'jb_saved_jobs', 'job_id', 'account_id')
            ->withTimestamps();
    }

    public function applicants(): HasMany
    {
        return $this->hasMany(JobApplication::class, 'job_id');
    }

    public function analytics(): HasMany
    {
        return $this->hasMany(Analytics::class, 'job_id');
    }

    public function canShowSavedJob(): bool
    {
        if (! auth('account')->check() || auth('account')->user()->isEmployer()) {
            return false;
        }

        return $this->is_saved !== -1;
    }

    public function canShowApplyJob(): bool
    {
        if (! auth('account')->check()) {
            return true;
        }

        if (auth('account')->user()->isEmployer()) {
            return false;
        }

        return $this->is_applied !== -1;
    }

    public function getApplicantsCountAttribute(): int
    {
        return (int) $this->number_of_applied;
    }

    public function getHasCompanyAttribute(): bool
    {
        return ! $this->hide_company && $this->company->id;
    }

    public function getCompanyLogoThumbAttribute(): string
    {
        if ($this->has_company) {
            return $this->company->logo_thumb;
        }

        $logo = theme_option('default_company_logo', theme_option('logo'));

        return RvMedia::getImageUrl($logo, null, false, RvMedia::getDefaultImage());
    }

    public function getCompanyNameAttribute(): ?string
    {
        if ($this->has_company) {
            return $this->company->name;
        }

        return null;
    }

    public function getCompanyUrlAttribute(): ?string
    {
        if ($this->has_company) {
            return $this->company->url;
        }

        return null;
    }

    public function getIsExpiredAttribute(): bool
    {
        if (! $this->expire_date || $this->never_expired) {
            return false;
        }

        return $this->expire_date->lte(Carbon::now());
    }

    public function isJobOpen(): bool
    {
        $now = Carbon::now();

        if ($this->status != JobStatusEnum::PUBLISHED) {
            return false;
        }

        if (! $this->never_expired && $this->expire_date && $now->greaterThan($this->expire_date)) {
            return false;
        }

        if ($this->application_closing_date && $now->greaterThan($this->application_closing_date)) {
            return false;
        }

        return true;
    }

    public function hasSalaryRange(): bool
    {
        return (bool) ($this->salary_from || $this->salary_to);
    }

    public function isNegotiableSalary(): bool
    {
        return ! $this->hide_salary && (string) $this->salary_type === SalaryTypeEnum::NEGOTIABLE;
    }

    public function shouldRenderLocationSchema(): bool
    {
        return (bool) ($this->country?->code ?: $this->country_name);
    }

    public function shouldExposeDirectApplyInSchema(): bool
    {
        if (! $this->isJobOpen()) {
            return false;
        }

        if (! $this->apply_url) {
            return true;
        }

        return $this->isLikelyDirectExternalApplyUrl();
    }

    public function isLikelyDirectExternalApplyUrl(): bool
    {
        if (! $this->apply_url) {
            return false;
        }

        $url = strtolower((string) $this->apply_url);
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        $path = strtolower((string) parse_url($url, PHP_URL_PATH));
        $query = strtolower((string) parse_url($url, PHP_URL_QUERY));
        $haystack = trim($host . ' ' . $path . ' ' . $query);

        if ($haystack === '') {
            return false;
        }

        foreach ([
            'greenhouse.io',
            'job-boards.greenhouse.io',
            'boards.greenhouse.io',
            'jobs.lever.co',
            'workable.com',
            'smartrecruiters.com',
            'ashbyhq.com',
            'bamboohr.com',
            'myworkdayjobs.com',
        ] as $knownDirectApplyHost) {
            if (str_contains($host, $knownDirectApplyHost)) {
                return true;
            }
        }

        foreach (['apply', 'application', 'jobapplication', 'candidate'] as $keyword) {
            if (str_contains($haystack, $keyword)) {
                return true;
            }
        }

        return false;
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(
            Tag::class,
            'jb_jobs_tags',
            'job_id',
            'tag_id'
        );
    }

    public function getEmployerColleaguesAttribute(?string $value): array
    {
        return json_decode((string) $value, true) ?: [];
    }

    public function setEmployerColleaguesAttribute(array $employerColleagues): void
    {
        $this->attributes['employer_colleagues'] = $employerColleagues ? json_encode($employerColleagues) : '';
    }

    public function getEffectiveExternalApplyBehavior(): string
    {
        // If job has specific setting, use it; otherwise use global setting
        return $this->external_apply_behavior ?: setting('job_board_external_apply_url_behavior', 'disabled');
    }

    public function shouldOpenExternalApplyUrlDirectly(): bool
    {
        return $this->getEffectiveExternalApplyBehavior() !== 'disabled';
    }

    public function getExternalApplyUrlTarget(): string
    {
        $behavior = $this->getEffectiveExternalApplyBehavior();

        return match ($behavior) {
            'new_tab' => '_blank',
            'current_tab' => '_self',
            default => '',
        };
    }

    public function getEmployerEmailsAttribute(): array
    {
        $emails = [];

        if ($this->author->email) {
            $emails[] = $this->author->email;
        }

        if (! empty($this->employer_colleagues)) {
            $emails = array_merge($emails, $this->employer_colleagues);
        }

        return $emails;
    }

    public function getLocationAttribute(): ?string
    {
        if ($this->is_remote && ! $this->state_name && ! $this->city_name && ! $this->country?->code) {
            return __('Remote');
        }

        $displayType = setting('job_board_job_location_display', 'state_and_country');
        $countryCode = $this->country?->code ?: '';

        $location = match ($displayType) {
            'city_state_and_country' => ($this->city_name ? $this->city_name . ', ' : '') . ($this->state_name ? $this->state_name . ', ' : '') . $countryCode,
            'city_and_state' => ($this->city_name ? $this->city_name . ', ' : '') . $this->state_name,
            default => ($this->state_name ? $this->state_name . ', ' : '') . $countryCode,
        };

        return trim(trim($location), ',');
    }

    public function customFields(): MorphMany
    {
        return $this->morphMany(CustomFieldValue::class, 'reference', 'reference_type', 'reference_id')->with('customField.options');
    }

    protected function customFieldsArray(): Attribute
    {
        return Attribute::make(
            get: function () {
                return CustomFieldValue::getCustomFieldValuesArray($this);
            },
        );
    }

    protected static function booted(): void
    {
        self::deleting(function (Job $job): void {
            $job->analytics()->delete();
            $job->applicants()->delete();
            $job->savedJobs()->detach();
            $job->skills()->detach();
            $job->jobTypes()->detach();
            $job->categories()->detach();
            $job->tags()->detach();
            $job->customFields()->delete();
        });

        self::updating(function (): void {
            JobBoardHelper::clearJobMaxPriceCache();
        });
    }

    public function newEloquentBuilder($query): BaseQueryBuilder
    {
        return new FilterJobsBuilder($query);
    }

    protected function buildFixedSalaryText(): string
    {
        $salaryRange = $this->displaySalaryRangeLabel();
        $from = (float) $this->salary_from;
        $to = (float) $this->salary_to;

        if ($from && $to) {
            return sprintf(
                '%s - %s / %s',
                $this->formatDisplayedSalaryAmount($from),
                $this->formatDisplayedSalaryAmount($to),
                $salaryRange
            );
        }

        if ($from) {
            return sprintf(
                '%s / %s',
                __('From :price', ['price' => $this->formatDisplayedSalaryAmount($from)]),
                $salaryRange
            );
        }

        if ($to) {
            return sprintf(
                '%s / %s',
                __('Upto :price', ['price' => $this->formatDisplayedSalaryAmount($to)]),
                $salaryRange
            );
        }

        return trans('plugins/job-board::messages.attractive');
    }
}
