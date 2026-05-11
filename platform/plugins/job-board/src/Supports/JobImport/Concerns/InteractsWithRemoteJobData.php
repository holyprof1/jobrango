<?php

namespace Botble\JobBoard\Supports\JobImport\Concerns;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

trait InteractsWithRemoteJobData
{
    protected function normalizeText(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim(html_entity_decode(strip_tags($value), ENT_QUOTES, 'UTF-8'));
        $value = preg_replace('/\s+/', ' ', $value);

        return $value !== '' ? $value : null;
    }

    protected function normalizeHtml(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value !== '' ? $value : null;
    }

    protected function normalizeList(mixed $value): array
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

            return $this->normalizeText((string) $item);
        }, $value))));
    }

    protected function parseDate(mixed $value): ?Carbon
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

    protected function absoluteUrl(string $url, ?string $baseUrl = null): string
    {
        if (Str::startsWith($url, ['http://', 'https://'])) {
            return $url;
        }

        if (! $baseUrl) {
            return $url;
        }

        $parts = parse_url($baseUrl);

        if (! Arr::has($parts, ['scheme', 'host'])) {
            return rtrim($baseUrl, '/') . '/' . ltrim($url, '/');
        }

        $root = sprintf('%s://%s', $parts['scheme'], $parts['host']);

        if (isset($parts['port'])) {
            $root .= ':' . $parts['port'];
        }

        if (Str::startsWith($url, '/')) {
            return $root . $url;
        }

        $path = isset($parts['path']) ? rtrim(dirname($parts['path']), '/\\') : '';

        return $root . ($path ? '/' . ltrim($path, '/') : '') . '/' . ltrim($url, '/');
    }
}
