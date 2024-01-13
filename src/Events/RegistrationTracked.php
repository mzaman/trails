<?php

namespace MasudZaman\Fingerprints\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use MasudZaman\Fingerprints\TrackableInterface;

class RegistrationTracked
{
    use Dispatchable, SerializesModels;

    public TrackableInterface $trackable;

    /**
     * Create a new event instance.
     *
     * @param TrackableInterface $trackable
     * @return void
     */
    public function __construct(TrackableInterface $trackable)
    {
        $this->trackable = $trackable;
    }
}
