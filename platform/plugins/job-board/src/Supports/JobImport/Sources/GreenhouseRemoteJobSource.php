<?php

namespace Botble\JobBoard\Supports\JobImport\Sources;

use Botble\JobBoard\Supports\JobImport\RemoteJobRecord;
use Illuminate\Support\Arr;

class GreenhouseRemoteJobSource extends AbstractRemoteJobSource
{
    public function fetch(): array
    {
        $boardToken = Arr::get($this->config, 'board_token');

        if (! $boardToken) {
            return [];
        }

        $response = $this->client()
            ->get(sprintf('https://boards-api.greenhouse.io/v1/boards/%s/jobs', $boardToken), [
                'content' => 'true',
            ])
            ->throw()
            ->json();

        $companyName = Arr::get($this->config, 'company_name')
            ?: data_get($response, 'meta.board_name')
            ?: str($boardToken)->replace('-', ' ')->title()->toString();

        return array_values(array_filter(array_map(function (array $job) use ($companyName) {
            $externalId = Arr::get($job, 'id');
            $title = $this->normalizeText(Arr::get($job, 'title'));
            $url = Arr::get($job, 'absolute_url');

            if (! $externalId || ! $title || ! $url) {
                return null;
            }

            return RemoteJobRecord::fromArray([
                'external_id' => $externalId,
                'title' => $title,
                'url' => $url,
                'apply_url' => $url,
                'description' => $this->normalizeText(Arr::get($job, 'content')),
                'content' => $this->normalizeHtml(html_entity_decode((string) Arr::get($job, 'content'))),
                'company' => [
                    'name' => $companyName,
                    'website' => Arr::get($this->config, 'company_website'),
                ],
                'categories' => array_merge(
                    Arr::pluck((array) Arr::get($job, 'departments', []), 'name'),
                    $this->normalizeList(Arr::get($this->config, 'categories', []))
                ),
                'tags' => array_merge(
                    Arr::pluck((array) Arr::get($job, 'offices', []), 'name'),
                    $this->sourceTags()
                ),
                'types' => $this->sourceTypes(),
                'location' => Arr::get($job, 'location.name'),
                'country' => Arr::get($this->config, 'country'),
                'published_at' => Arr::get($job, 'updated_at'),
                'source_updated_at' => Arr::get($job, 'updated_at'),
                'metadata' => [
                    'source' => 'greenhouse',
                    'raw' => $job,
                ],
            ]);
        }, Arr::get($response, 'jobs', []))));
    }
}
