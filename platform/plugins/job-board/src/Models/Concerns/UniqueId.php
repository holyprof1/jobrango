<?php

namespace Botble\JobBoard\Models\Concerns;

use Illuminate\Support\Str;

trait UniqueId
{
    protected static function bootUniqueId(): void
    {
        static::creating(function ($model): void {
            if (! $model->unique_id) {
                $model->unique_id = $model->generateUniqueId();
            }
        });
    }

    public function generateUniqueId(bool $force = false): float|string|null
    {
        $sequenceId = $this->prefixedSequenceId();

        if (
            ! $force
            && (
                ! setting('job_board_auto_generate_unique_id', true) ||
                ! setting('job_board_unique_id_format')
            )
        ) {
            return $sequenceId;
        }

        $setting = (string) setting('job_board_unique_id_format');

        if ($setting === '') {
            return $sequenceId;
        }

        if (Str::contains($setting, '{id}')) {
            return str_replace('{id}', $sequenceId, $setting);
        }

        if (! Str::contains($setting, ['[%s]', '[%d]', '[%S]', '[%D]', '%s', '%d'])) {
            return $setting . $sequenceId;
        }

        $uniqueId = str_replace(
            ['[%s]', '[%S]'],
            strtoupper(Str::random(5)),
            $setting
        );

        $uniqueId = str_replace(
            ['[%d]', '[%D]'],
            (string) mt_rand(10000, 99999),
            $uniqueId
        );

        foreach (explode('%s', $uniqueId) as $ignored) {
            $uniqueId = preg_replace('/%s/i', strtoupper(Str::random(1)), $uniqueId, 1);
        }

        foreach (explode('%d', $uniqueId) as $ignored) {
            $uniqueId = preg_replace('/%d/i', (string) mt_rand(0, 9), $uniqueId, 1);
        }

        if ($this->query()->where('unique_id', $uniqueId)->exists()) {
            return $uniqueId . (mt_rand(10000, 99999) + time());
        }

        return $uniqueId;
    }

    public function prefixedSequenceId(): string
    {
        $key = $this->getKey() ?: (($this->newQuery()->max($this->getKeyName()) ?? 0) + 1);

        return '1' . str_pad((string) $key, 5, '0', STR_PAD_LEFT);
    }
}
