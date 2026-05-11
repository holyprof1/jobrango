<?php

namespace Botble\JobBoard\Supports\JobImport;

use Botble\JobBoard\Supports\JobImport\Sources\GreenhouseRemoteJobSource;
use Botble\JobBoard\Supports\JobImport\Sources\HtmlRemoteJobSource;
use Botble\JobBoard\Supports\JobImport\Sources\LeverRemoteJobSource;
use Botble\JobBoard\Supports\JobImport\Sources\SmartRecruitersRemoteJobSource;
use Botble\JobBoard\Supports\JobImport\Sources\WorkableRemoteJobSource;
use Illuminate\Support\Arr;
use InvalidArgumentException;

class RemoteJobSourceFactory
{
    public function make(string $key, array $config): RemoteJobSourceInterface
    {
        $class = match (Arr::get($config, 'driver')) {
            'greenhouse' => GreenhouseRemoteJobSource::class,
            'lever' => LeverRemoteJobSource::class,
            'smartrecruiters' => SmartRecruitersRemoteJobSource::class,
            'workable' => WorkableRemoteJobSource::class,
            'html' => HtmlRemoteJobSource::class,
            default => null,
        };

        if (! $class) {
            throw new InvalidArgumentException(sprintf('Unsupported job import driver [%s].', Arr::get($config, 'driver')));
        }

        return new $class($key, $config);
    }
}
