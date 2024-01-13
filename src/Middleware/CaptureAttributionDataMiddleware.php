<?php

namespace MasudZaman\Trails\Middleware;

use Closure;

use Illuminate\Http\Request;
use MasudZaman\Trails\TrackingFilterInterface;
use MasudZaman\Trails\TrackingLoggerInterface;

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
