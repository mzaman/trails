<?php

namespace MasudZaman\Fingerprints\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use MasudZaman\Fingerprints\Events\RegistrationTracked;
use MasudZaman\Fingerprints\TrackableInterface;
use MasudZaman\Fingerprints\Visit;

class AssignPreviousVisits implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $fingerprint;
    public TrackableInterface $trackable;

    public function __construct(string $fingerprint, TrackableInterface $trackable)
    {
        $this->fingerprint = $fingerprint;
        $this->trackable = $trackable;
    }

    public function handle()
    {
        Visit::unassignedPreviousVisits($this->fingerprint)->update(
            [
                config('fingerprints.column_name') => $this->trackable->id,
            ]
        );

        event(new RegistrationTracked($this->trackable));
    }
}
