<?php

namespace App\Console\Commands;

use App\Services\Organizations\LegacyOrganizationBackfillService;
use Illuminate\Console\Command;

class BackfillOrganizations extends Command
{
    protected $signature = 'organizations:backfill {--dry-run : Report changes without writing them}';
    protected $description = 'Idempotently map legacy organizers, events, and security officers to organizations';

    public function handle(LegacyOrganizationBackfillService $service): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $report = $service->run($dryRun);
        $this->info($dryRun ? 'Dry-run report; no records were changed.' : 'Organization backfill completed.');
        $this->table(['Metric', 'Count'], collect($report)->map(fn ($count, $metric) => [$metric, $count])->values()->all());

        return self::SUCCESS;
    }
}
