<?php

namespace Botble\JobBoard\Supports\JobImport\Sources;

use Botble\JobBoard\Supports\JobImport\RemoteJobRecord;
use Illuminate\Support\Arr;

class LeverRemoteJobSource extends AbstractRemoteJobSource
{
    public function fetch(): array
    {
        $site = Arr::get($this->config, 'site');

        if (! $site) {
            return [];
        }

        $response = $this->client()
            ->get(sprintf('https://api.lever.co/v0/postings/%s', $site), [
                'mode' => 'json',
            ])
            ->throw()
            ->json();

        $companyName = Arr::get($this->config, 'company_name') ?: str($site)->replace('-', ' ')->title()->toString();

        return array_values(array_filter(array_map(function (array $job) use ($companyName) {
            $externalId = Arr::get($job, 'id');
            $title = $this->normalizeText(Arr::get($job, 'text'));
            $url = Arr::get($job, 'hostedUrl') ?: Arr::get($job, 'applyUrl');

            if (! $externalId || ! $title || ! $url) {
                return null;
            }

            $categories = Arr::where((array) Arr::get($job, 'categories', []), fn ($value) => filled($value));

            return RemoteJobRecord::fromArray([
                'external_id' => $externalId,
                'title' => $title,
                'url' => $url,
                'apply_url' => Arr::get($job, 'applyUrl') ?: $url,
                'description' => $this->normalizeText(Arr::get($job, 'descriptionPlain')),
                'content' => $this->normalizeHtml(Arr::get($job, 'description')),
                'company' => [
                    'name' => $companyName,
                    'website' => Arr::get($this->config, 'company_website'),
                ],
                'categories' => array_merge(array_values($categories), $this->normalizeList(Arr::get($this->config, 'categories', []))),
                'types' => array_merge(
                    $this->normalizeList(Arr::get($job, 'categories.commitment')),
                    $this->sourceTypes()
                ),
                'tags' => array_merge(
                    $this->normalizeList(Arr::get($job, 'workplaceType')),
                    $this->sourceTags()
                ),
                'location' => Arr::get($job, 'categories.location'),
                'country' => Arr::get($this->config, 'country'),
                'published_at' => Arr::get($job, 'createdAt'),
                'source_updated_at' => Arr::get($job, 'updatedAt'),
                'metadata' => [
                    'source' => 'lever',
                    'raw' => $job,
                ],
            ]);
        }, is_array($response) ? $response : [])));
    }
}
