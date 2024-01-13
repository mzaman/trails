<?php

namespace MasudZaman\Trails;

use Illuminate\Http\Request;
use MasudZaman\Trails\Jobs\TrackVisit;
use Illuminate\Support\Facades\Auth;

class TrackingLogger implements TrackingLoggerInterface
{
    /**
     * The Request instance.
     *
     * @var \Illuminate\Http\Request
     */
    protected Request $request;

    /**
     * Track the request.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Request
     */
    public function track(Request $request): Request
    {
        $this->request = $request;

        \Log::info('TrackingLogger track...', [Auth::user(), auth()->user(), auth()->guard(config('trails.guard'))->user(), request()->user()]);
        
        $job = new TrackVisit($this->captureAttributionData(), Auth::user() ? Auth::user()->id : null);
        if (config('trails.async') == true) {
            dispatch($job);
        } else {
            $job->handle();
        }

        return $this->request;
    }

    /**
     * @return array
     */
    protected function captureAttributionData()
    {
        $attributes = array_merge(
            [
                'trail'         => $this->request->trail(),
                'ip'                => $this->captureIp(),
                'landing_domain'    => $this->captureLandingDomain(),
                'landing_page'      => $this->captureLandingPage(),
                'landing_params'    => $this->captureLandingParams(),
                'referral'          => $this->captureReferral(),
                'gclid'             => $this->captureGCLID(),
            ],
            $this->captureUTM(),
            $this->captureReferrer(),
            $this->getCustomParameter()
        );

        // \Log::info('headers', $this->request->headers->all());
        \Log::info('capture attribution', $attributes);

        return array_map(fn (?string $item) => is_string($item) ? substr($item, 0, 255) : $item, $attributes);
    }

    /**
     * @return array
     */
    protected function getCustomParameter()
    {
        $arr = [];

        if (config('trails.custom_parameters')) {
            foreach (config('trails.custom_parameters') as $parameter) {
                $arr[$parameter] = $this->request->input($parameter);
            }
        }

        return $arr;
    }

    /**
     * @return string|null
     */
    protected function captureIp()
    {
        if (! config('trails.attribution_ip')) {
            return null;
        }

        return $this->request->ip();
    }

    /**
     * @return string
     */
    protected function captureLandingDomain()
    {
        return $this->request->server('SERVER_NAME');
    }

    /**
     * @return string
     */
    protected function captureLandingPage()
    {
        return $this->request->path();
    }

    /**
     * @return string
     */
    protected function captureLandingParams()
    {
        return $this->request->getQueryString();
    }

    /**
     * @return array
     */
    protected function captureUTM()
    {
        $parameters = ['utm_source', 'utm_campaign', 'utm_medium', 'utm_term', 'utm_content'];

        $utm = [];

        foreach ($parameters as $parameter) {
            if ($this->request->has($parameter)) {
                $utm[$parameter] = $this->request->input($parameter);
            } else {
                $utm[$parameter] = null;
            }
        }

        return $utm;
    }

    /**
     * @return array
     */
    protected function captureReferrer()
    {
        $referrer = [];

        $referrer['referrer_url'] = $this->request->headers->get('referer');

        $parsedUrl = parse_url($referrer['referrer_url']);

        $referrer['referrer_domain'] = isset($parsedUrl['host']) ? $parsedUrl['host'] : null;

        return $referrer;
    }

    /**
     * @return string
     */
    protected function captureGCLID()
    {
        return $this->request->input('gclid');
    }

    /**
     * @return string
     */
    protected function captureReferral()
    {
        return $this->request->input('ref');
    }
}
