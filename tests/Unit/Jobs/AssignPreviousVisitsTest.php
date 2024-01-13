<?php

namespace MasudZaman\Fingerprints\Tests\Unit\Jobs;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use MasudZaman\Fingerprints\Events\RegistrationTracked;
use MasudZaman\Fingerprints\Jobs\AssignPreviousVisits;
use MasudZaman\Fingerprints\Tests\TestCase;
use MasudZaman\Fingerprints\TrackableInterface;
use Mockery\MockInterface;

class AssignPreviousVisitsTest extends TestCase
{
    use RefreshDatabase;

    public function test_emits_registration_tracked_event()
    {
        $trackable = $this->mock(TrackableInterface::class, function (MockInterface $mock) {
            $mock->id = 123;
        });

        Event::fake();

        $job = new AssignPreviousVisits('test-fingerprint', $trackable);
        $job->handle(); // We are not checking the "queue" part of the job, only that it does actually dispatch the event

        Event::assertDispatched(RegistrationTracked::class, function ($event) use ($trackable) {
            return $event->trackable === $trackable
                && $event->fingerprint = 'test-fingerprint';
        });
    }
}
