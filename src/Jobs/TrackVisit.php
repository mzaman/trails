<?php

namespace MasudZaman\Trails\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use MasudZaman\Trails\Visit;

class TrackVisit implements ShouldQueue
{
    use Queueable;

    protected array $attributionData;
    public $trackableId;

    public function __construct(array $attributionData, $trackableId = null)
    {
        $this->attributionData = $attributionData;
        $this->trackableId = $trackableId;
    }

    public function handle()
    {
        Visit::create(array_merge([
            config('trails.column_name') => $this->trackableId,
        ], $this->attributionData));
    }
}
