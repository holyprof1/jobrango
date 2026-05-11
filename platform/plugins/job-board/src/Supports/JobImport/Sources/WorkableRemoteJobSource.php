<?php

namespace Botble\JobBoard\Supports\JobImport\Sources;

use Botble\JobBoard\Supports\JobImport\RemoteJobRecord;
use Illuminate\Support\Arr;

class WorkableRemoteJobSource extends AbstractRemoteJobSource
{
    public function fetch(): array
    {
        $account = Arr::get($this->config, 'account');

        if (! $account) {
            return [];
        }

        $response = $this->client()
            ->get(sprintf('https://apply.workable.com/api/v1/widget/accounts/%s', $account), [
                'details' => 'true',
            ])
            ->throw()
            ->json();

        $companyName = Arr::get($response, 'account.name')
            ?: Arr::get($this->config, 'company_name')
            ?: str($account)->replace('-', ' ')->title()->toString();

        return array_values(array_filter(array_map(function (array $job) use ($companyName) {
            $externalId = Arr::get($job, 'shortcode') ?: Arr::get($job, 'id');
            $title = $this->normalizeText(Arr::get($job, 'title'));
            $url = Arr::get($job, 'url');

            if (! $externalId || ! $title || ! $url) {
                return null;
            }

            return RemoteJobRecord::fromArray([
                'external_id' => $externalId,
                'title' => $title,
                'url' => $url,
                'apply_url' => $url,
                'description' => $this->normalizeText(Arr::get($job, 'description')),
                'content' => $this->normalizeHtml(Arr::get($job, 'description')),
                'company' => [
                    'name' => $companyName,
                    'website' => Arr::get($this->config, 'company_website'),
                ],
                'categories' => array_merge(
                    $this->normalizeList(Arr::get($job, 'department')),
                    $this->normalizeList(Arr::get($this->config, 'categories', []))
                ),
                'types' => array_merge(
                    $this->normalizeList(Arr::get($job, 'employment_type')),
                    $this->sourceTypes()
                ),
                'tags' => $this->sourceTags(),
                'location' => Arr::get($job, 'location.city') ?: Arr::get($job, 'location.location_str'),
                'country' => Arr::get($job, 'location.country') ?: Arr::get($this->config, 'country'),
                'city' => Arr::get($job, 'location.city'),
                'published_at' => Arr::get($job, 'published'),
                'source_updated_at' => Arr::get($job, 'updated_at'),
                'metadata' => [
                    'source' => 'workable',
                    'raw' => $job,
                ],
            ]);
        }, Arr::get($response, 'jobs', []))));
    }
}
