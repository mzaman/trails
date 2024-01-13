<?php

namespace MasudZaman\Fingerprints\Middleware;

use Closure;

use Illuminate\Http\Request;
use MasudZaman\Fingerprints\TrackingFilterInterface;
use MasudZaman\Fingerprints\TrackingLoggerInterface;

class CaptureAttributionDataMiddleware
{
    protected TrackingFilterInterface $filter;

    protected TrackingLoggerInterface $logger;

    public function __construct(TrackingFilterInterface $filter, TrackingLoggerInterface $logger)
    {
        $this->filter = $filter;
        $this->logger = $logger;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($this->filter->shouldTrack($request)) {
            $request = $this->logger->track($request);
        }

        return $next($request);
    }
}
