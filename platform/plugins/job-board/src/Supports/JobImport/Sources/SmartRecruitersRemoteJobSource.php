<?php

namespace Botble\JobBoard\Supports\JobImport\Sources;

use Botble\JobBoard\Supports\JobImport\RemoteJobRecord;
use Illuminate\Support\Arr;

class SmartRecruitersRemoteJobSource extends AbstractRemoteJobSource
{
    public function fetch(): array
    {
        $companyIdentifier = Arr::get($this->config, 'company_identifier');

        if (! $companyIdentifier) {
            return [];
        }

        $companyName = Arr::get($this->config, 'company_name') ?: str($companyIdentifier)->replace('-', ' ')->title()->toString();
        $jobs = [];
        $offset = 0;
        $limit = 100;

        do {
            $response = $this->client()
                ->get(sprintf('https://api.smartrecruiters.com/v1/companies/%s/postings', $companyIdentifier), [
                    'limit' => $limit,
                    'offset' => $offset,
                ])
                ->throw()
                ->json();

            $items = Arr::get($response, 'content', []);

            foreach ($items as $item) {
                $postingId = Arr::get($item, 'id');

                if (! $postingId) {
                    continue;
                }

                $detail = $this->client()
                    ->get(sprintf('https://api.smartrecruiters.com/v1/companies/%s/postings/%s', $companyIdentifier, $postingId))
                    ->throw()
                    ->json();

                $jobs[] = $this->mapRecord($item, $detail, $companyName);
            }

            $offset += $limit;
        } while (! empty($items) && count($items) === $limit);

        return array_values(array_filter($jobs));
    }

    protected function mapRecord(array $item, array $detail, string $companyName): ?RemoteJobRecord
    {
        $externalId = Arr::get($item, 'id') ?: Arr::get($detail, 'id');
        $title = $this->normalizeText(Arr::get($item, 'name') ?: Arr::get($detail, 'name'));
        $url = Arr::get($item, 'ref') ?: Arr::get($detail, 'ref');

        if (! $externalId || ! $title || ! $url) {
            return null;
        }

        $sections = collect((array) Arr::get($detail, 'jobAd.sections', []))
            ->map(function (array $section) {
                $title = $this->normalizeText(Arr::get($section, 'title'));
                $text = $this->normalizeHtml(Arr::get($section, 'text'));

                if (! $text) {
                    return null;
                }

                return $title ? sprintf('<h3>%s</h3>%s', e($title), $text) : $text;
            })
            ->filter()
            ->implode(PHP_EOL);

        return RemoteJobRecord::fromArray([
            'external_id' => $externalId,
            'title' => $title,
            'url' => $url,
            'apply_url' => Arr::get($detail, 'applyUrl') ?: $url,
            'description' => $this->normalizeText($sections),
            'content' => $sections,
            'company' => [
                'external_id' => Arr::get($detail, 'company.id'),
                'name' => Arr::get($detail, 'company.name') ?: $companyName,
                'website' => Arr::get($this->config, 'company_website'),
            ],
            'categories' => array_merge(
                $this->normalizeList(Arr::get($detail, 'department.label')),
                $this->normalizeList(Arr::get($this->config, 'categories', []))
            ),
            'types' => array_merge(
                $this->normalizeList(Arr::get($detail, 'typeOfEmployment.label')),
                $this->sourceTypes()
            ),
            'tags' => $this->sourceTags(),
            'location' => Arr::get($detail, 'location.fullLocation') ?: Arr::get($item, 'location'),
            'country' => Arr::get($detail, 'location.country') ?: Arr::get($this->config, 'country'),
            'city' => Arr::get($detail, 'location.city'),
            'published_at' => Arr::get($detail, 'releasedDate'),
            'closing_at' => Arr::get($detail, 'jobAd.endDate'),
            'source_updated_at' => Arr::get($detail, 'updatedOn'),
            'metadata' => [
                'source' => 'smartrecruiters',
                'raw' => $detail,
            ],
        ]);
    }
}
