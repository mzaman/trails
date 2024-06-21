<?php

namespace MasudZaman\Trails;

use Illuminate\Http\Request;
use MasudZaman\Trails\Jobs\TrackVisit;
use Illuminate\Support\Facades\Auth;
use MasudZaman\Trails\CommonTrait;

/**
 * Class TrackingLogger
 * Implements TrackingLoggerInterface for tracking user visits and attributions.
 */
class TrackingLogger implements TrackingLoggerInterface
{
    use CommonTrait;

    /**
     * The Request instance.
     *
     * @var \Illuminate\Http\Request
     */
    protected Request $request;

    /**
     * Indicates if the tracking should be done asynchronously.
     *
     * @var bool
     */
    protected bool $async;

    /**
     * The name of the queue to be used for asynchronous tracking.
     *
     * @var string
     */
    protected string $queue;

    /**
     * The queue connection to be used.
     *
     * @var string
     */
    protected string $queueConnection;

    /**
     * TrackingLogger constructor.
     * Initializes the logger with configuration settings.
     */
    public function __construct()
    {
        $this->async = config('trails.async', false);
        $this->queue = config('trails.queue', 'default');
        $this->queueConnection = config('trails.queueConnection', 'database');
    }

    /**
     * Track the request and log attribution data.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Request
     */
    public function track(Request $request): Request
    {
        $this->request = $request;

        $job = new TrackVisit(
            $this->captureAttributionData(), 
            Auth::check() ? Auth::user()->id : null
        );

        if ($this->async) {
            dispatch($job)
                ->onConnection($this->queueConnection)
                ->onQueue($this->queue);
        } else {
            $job->handle();
        }

        return $this->request;
    }

    /**
     * Capture attribution data from the request.
     *
     * @return array
     */
    protected function captureAttributionData(): array
    {
        $attributes = array_merge(
            [
                'trail' => $this->request->trail(),
                'ip' => $this->captureIp(),
                'landing_domain' => $this->captureLandingDomain(),
                'landing_page' => $this->captureLandingPage(),
                'landing_params' => $this->captureLandingParams(),
                'referral' => $this->captureReferral(),
                'gclid' => $this->captureGCLID(),
            ],
            $this->captureCampaign(),
            $this->captureReferrer(),
            $this->getCustomParameter()
        );

        return array_map(fn (?string $item) => is_string($item) ? substr($item, 0, 255) : $item, $attributes);
    }

    /**
     * Capture custom parameters from the request.
     *
     * @return array
     */
    protected function getCustomParameter(): array
    {
        $customParameters = config('trails.custom_parameters', []);
        $parameters = [];

        foreach ($customParameters as $parameter) {
            $parameters[$parameter] = $this->request->input($parameter);
        }

        return $parameters;
    }

    /**
     * Capture the client's IP address if allowed by configuration.
     *
     * @return string|null
     */
    protected function captureIp(): ?string
    {
        return config('trails.attribution_ip', false) ? $this->request->ip() : null;
    }

    /**
     * Capture the landing domain from the server request.
     *
     * @return string
     */
    protected function captureLandingDomain(): string
    {
        return $this->request->server('SERVER_NAME');
    }

    /**
     * Capture the landing page path from the request.
     *
     * @return string
     */
    protected function captureLandingPage(): string
    {
        return $this->request->path();
    }

    /**
     * Capture the query string parameters from the request.
     *
     * @return string|null
     */
    protected function captureLandingParams(): ?string
    {
        return $this->request->getQueryString();
    }

    /**
     * Capture the referrer URL and domain from the request headers.
     *
     * @return array
     */
    protected function captureReferrer(): array
    {
        $referrerUrl = $this->request->headers->get('referer');
        $parsedUrl = parse_url($referrerUrl);

        return [
            'referrer_url' => $referrerUrl,
            'referrer_domain' => $parsedUrl['host'] ?? null,
        ];
    }

    /**
     * Capture the Google Click Identifier (GCLID) from the request.
     *
     * @return string|null
     */
    protected function captureGCLID(): ?string
    {
        return $this->request->input('gclid');
    }

    /**
     * Capture the referral parameter from the request.
     *
     * @return string|null
     */
    protected function captureReferral(): ?string
    {
        return $this->request->input('ref');
    }
}