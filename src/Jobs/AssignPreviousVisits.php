<?php

namespace MasudZaman\Trails\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use MasudZaman\Trails\Events\RegistrationTracked;
use MasudZaman\Trails\TrackableInterface;
use MasudZaman\Trails\Visit;

class AssignPreviousVisits implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $trail;
    public TrackableInterface $trackable;

    public function __construct(string $trail, TrackableInterface $trackable)
    {
        $this->trail = $trail;
        $this->trackable = $trackable;
    }

    public function handle()
    {
        Visit::unassignedPreviousVisits($this->trail)->update(
            [
                config('trails.column_name') => $this->trackable->id,
            ]
        );

        event(new RegistrationTracked($this->trackable));
    }
}
