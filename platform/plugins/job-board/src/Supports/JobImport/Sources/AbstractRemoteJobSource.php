<?php

namespace Botble\JobBoard\Supports\JobImport\Sources;

use Botble\JobBoard\Supports\JobImport\Concerns\InteractsWithRemoteJobData;
use Botble\JobBoard\Supports\JobImport\RemoteJobSourceInterface;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

abstract class AbstractRemoteJobSource implements RemoteJobSourceInterface
{
    use InteractsWithRemoteJobData;

    public function __construct(
        protected string $key,
        protected array $config
    ) {
    }

    public function key(): string
    {
        return $this->key;
    }

    public function label(): string
    {
        return (string) Arr::get($this->config, 'label', $this->key);
    }

    public function enabled(): bool
    {
        return (bool) Arr::get($this->config, 'enabled', false);
    }

    protected function client(): PendingRequest
    {
        return Http::timeout((int) Arr::get($this->config, 'timeout', 20))
            ->retry(2, 500)
            ->withHeaders((array) Arr::get($this->config, 'headers', []))
            ->acceptJson();
    }

    protected function baseUrl(): ?string
    {
        return Arr::get($this->config, 'base_url');
    }

    protected function sourceTags(): array
    {
        return $this->normalizeList(Arr::get($this->config, 'tags', []));
    }

    protected function sourceTypes(): array
    {
        return $this->normalizeList(Arr::get($this->config, 'types', []));
    }
}
