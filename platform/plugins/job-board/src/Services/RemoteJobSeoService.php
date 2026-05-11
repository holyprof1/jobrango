<?php

namespace Botble\JobBoard\Services;

use Botble\Base\Facades\MetaBox;
use Botble\JobBoard\Models\Job;
use Botble\JobBoard\Supports\JobImport\RemoteJobRecord;
use Illuminate\Support\Str;

class RemoteJobSeoService
{
    public function buildDescription(RemoteJobRecord $record): string
    {
        $parts = array_filter([
            $record->description,
            $record->location ? 'Location: ' . $record->location : null,
            ! empty($record->company['name']) ? 'Company: ' . $record->company['name'] : null,
        ]);

        return Str::limit(implode(' ', $parts), 220, '');
    }

    public function buildContent(RemoteJobRecord $record): string
    {
        if ($record->content) {
            return $record->content;
        }

        $description = e($record->description ?: 'New opportunity available now.');
        $sections = [
            "<p>{$description}</p>",
        ];

        $highlights = array_filter([
            $record->location ? 'Location: ' . e($record->location) : null,
            ! empty($record->company['name']) ? 'Company: ' . e($record->company['name']) : null,
            $record->salaryFrom || $record->salaryTo ? 'Compensation: ' . e(trim(($record->salaryFrom ? number_format($record->salaryFrom) : '') . ($record->salaryTo ? ' - ' . number_format($record->salaryTo) : ''))) : null,
            $record->applyUrl ? 'Apply: ' . e($record->applyUrl) : null,
        ]);

        if ($highlights) {
            $sections[] = '<ul><li>' . implode('</li><li>', $highlights) . '</li></ul>';
        }

        if ($record->applyUrl) {
            $sections[] = '<p>Applications for this role are completed on the employer&apos;s site.</p>';
        }

        return implode(PHP_EOL, $sections);
    }

    public function save(Job $job, RemoteJobRecord $record): void
    {
        $companyName = $record->company['name'] ?? null;
        $location = $record->location ?: $record->city ?: $record->state ?: $record->country;

        $titleParts = array_filter([
            $record->title,
            $location ? 'in ' . $location : null,
            $companyName ? 'at ' . $companyName : null,
        ]);

        MetaBox::saveMetaBoxData($job, 'seo_meta', [
            'seo_title' => Str::limit(implode(' ', $titleParts), 110, ''),
            'seo_description' => $this->buildDescription($record),
            'index' => 'index',
        ]);
    }
}
