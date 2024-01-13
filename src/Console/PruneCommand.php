<?php

namespace MasudZaman\Trails\Console;

use Illuminate\Console\Command;
use MasudZaman\Trails\Visit;

class PruneCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trails:prune {--days= : The number of days to retain unassigned Trails data}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prune stale (ie unassigned) entries from the Trails database';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $days = $this->option('days') ?? config('trails.attribution_duration') / (60 * 60 * 24);

        return Visit::prunable($days)->delete();
    }
}
