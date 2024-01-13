<?php

namespace MasudZaman\Fingerprints\Console;

use Illuminate\Console\Command;
use MasudZaman\Fingerprints\Visit;

class PruneCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fingerprints:prune {--days= : The number of days to retain unassigned Fingerprints data}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prune stale (ie unassigned) entries from the Fingerprints database';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $days = $this->option('days') ?? config('fingerprints.attribution_duration') / (60 * 60 * 24);

        return Visit::prunable($days)->delete();
    }
}
