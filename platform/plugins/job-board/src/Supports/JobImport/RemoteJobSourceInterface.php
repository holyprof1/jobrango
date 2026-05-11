<?php

namespace Botble\JobBoard\Supports\JobImport;

interface RemoteJobSourceInterface
{
    public function key(): string;

    public function label(): string;

    public function enabled(): bool;

    /**
     * @return array<RemoteJobRecord>
     */
    public function fetch(): array;
}
