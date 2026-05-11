<?php

namespace Botble\JobBoard\Supports\JobImport;

use Carbon\Carbon;
use Illuminate\Support\Arr;

class RemoteJobRecord
{
    public function __construct(
        public readonly string $externalId,
        public readonly string $title,
        public readonly string $url,
        public readonly ?string $applyUrl = null,
        public readonly ?string $description = null,
        public readonly ?string $content = null,
        public readonly array $company = [],
        public readonly array $categories = [],
        public readonly array $types = [],
        public readonly array $tags = [],
        public readonly ?string $location = null,
        public readonly ?string $country = null,
        public readonly ?string $state = null,
        public readonly ?string $city = null,
        public readonly ?string $address = null,
        public readonly ?float $salaryFrom = null,
        public readonly ?float $salaryTo = null,
        public readonly ?string $salaryRange = null,
        public readonly bool $isRemote = false,
        public readonly ?Carbon $publishedAt = null,
        public readonly ?Carbon $expiresAt = null,
        public readonly ?Carbon $closingAt = null,
        public readonly ?Carbon $sourceUpdatedAt = null,
        public readonly array $metadata = []
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            externalId: (string) Arr::get($data, 'external_id'),
            title: trim((string) Arr::get($data, 'title')),
            url: trim((string) Arr::get($data, 'url')),
            applyUrl: self::nullableString(Arr::get($data, 'apply_url')),
            description: self::nullableString(Arr::get($data, 'description')),
            content: self::nullableHtml(Arr::get($data, 'content')),
            company: is_array(Arr::get($data, 'company')) ? Arr::get($data, 'company') : [],
            categories: self::normalizeList(Arr::get($data, 'categories', [])),
            types: self::normalizeList(Arr::get($data, 'types', [])),
            tags: self::normalizeList(Arr::get($data, 'tags', [])),
            location: self::nullableString(Arr::get($data, 'location')),
            country: self::nullableString(Arr::get($data, 'country')),
            state: self::nullableString(Arr::get($data, 'state')),
            city: self::nullableString(Arr::get($data, 'city')),
            address: self::nullableString(Arr::get($data, 'address')),
            salaryFrom: self::nullableFloat(Arr::get($data, 'salary_from')),
            salaryTo: self::nullableFloat(Arr::get($data, 'salary_to')),
            salaryRange: self::nullableString(Arr::get($data, 'salary_range')),
            isRemote: (bool) Arr::get($data, 'is_remote', false),
            publishedAt: self::nullableDate(Arr::get($data, 'published_at')),
            expiresAt: self::nullableDate(Arr::get($data, 'expires_at')),
            closingAt: self::nullableDate(Arr::get($data, 'closing_at')),
            sourceUpdatedAt: self::nullableDate(Arr::get($data, 'source_updated_at')),
            metadata: is_array(Arr::get($data, 'metadata')) ? Arr::get($data, 'metadata') : []
        );
    }

    protected static function normalizeList(mixed $value): array
    {
        if (is_string($value)) {
            $value = preg_split('/[,|]/', $value) ?: [];
        }

        if (! is_array($value)) {
            return [];
        }

        return array_values(array_filter(array_unique(array_map(function ($item) {
            if (is_array($item)) {
                $item = Arr::first(array_filter($item));
            }

            return self::nullableString($item);
        }, $value))));
    }

    protected static function nullableString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim(strip_tags((string) $value));

        return $value !== '' ? $value : null;
    }

    protected static function nullableFloat(mixed $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_string($value)) {
            $value = preg_replace('/[^\d.\-]/', '', $value);
        }

        return is_numeric($value) ? (float) $value : null;
    }

    protected static function nullableDate(mixed $value): ?Carbon
    {
        if (empty($value)) {
            return null;
        }

        try {
            return Carbon::parse($value);
        } catch (\Throwable) {
            return null;
        }
    }

    protected static function nullableHtml(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value !== '' ? $value : null;
    }
}
