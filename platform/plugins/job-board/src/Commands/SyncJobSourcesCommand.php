<?php

namespace Botble\JobBoard\Commands;

use Botble\JobBoard\Services\RemoteJobSyncService;
use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand('cms:jobs:sync-sources', 'Import jobs from configured remote sources')]
class SyncJobSourcesCommand extends Command
{
    protected $signature = 'cms:jobs:sync-sources {source? : Optional configured source key} {--keep-missing : Keep previously imported jobs that are no longer returned by the source}';

    public function handle(RemoteJobSyncService $syncService): int
    {
        $results = $syncService->sync(
            $this->argument('source'),
            $this->option('keep-missing') ? false : null
        );

        if ($results === []) {
            $this->components->warn('No enabled remote job sources were found.');

            return self::SUCCESS;
        }

        foreach ($results as $key => $result) {
            $this->components->twoColumnDetail(sprintf('%s (%s)', $result['label'], $key), sprintf(
                'fetched %d, created %d, updated %d, closed %d',
                $result['fetched'],
                $result['created'],
                $result['updated'],
                $result['closed']
            ));
        }

        $totals = collect($results)->reduce(function (array $carry, array $item) {
            foreach (['fetched', 'created', 'updated', 'closed'] as $key) {
                $carry[$key] += $item[$key];
            }

            return $carry;
        }, ['fetched' => 0, 'created' => 0, 'updated' => 0, 'closed' => 0]);

        $this->newLine();
        $this->components->info(sprintf(
            'Remote sync complete. Fetched %d jobs, created %d, updated %d, closed %d.',
            $totals['fetched'],
            $totals['created'],
            $totals['updated'],
            $totals['closed']
        ));

        return self::SUCCESS;
    }
}
