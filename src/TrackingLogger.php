<?php

namespace MasudZaman\Trails;

use Illuminate\Http\Request;
use MasudZaman\Trails\Jobs\TrackVisit;
use Illuminate\Support\Facades\Auth;
use MasudZaman\Trails\CommonTrait;

class TrackingLogger implements TrackingLoggerInterface
{
    use CommonTrait;

    /**
     * The Request instance.
     *
     * @var \Illuminate\Http\Request
     */
    protected Request $request;
    /** @var bool */
    protected $async;
    /** @var string */
    protected $queue;
    /** @var string */
    protected $queueConnection;

    /**
     * TrackingLogger constructor.
     *
     */
    public function __construct()
    {
        $this->async = config('trails.async', false);
        $this->queue = config('trails.queue', 'default');
        $this->queueConnection = config('trails.queueConnection', 'database');
    }
    
    /**
     * Track the request.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Request
     */
    public function track(Request $request): Request
    {
        $this->request = $request;

        $job = new TrackVisit($this->captureAttributionData(), Auth::user() ? Auth::user()->id : null);
        if ($this->async == true) {
            dispatch($job)
            ->onConnection($this->queueConnection)
            ->onQueue($this->queue);
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
                'trail'             => $this->request->trail(),
                'ip'                => $this->captureIp(),
                'landing_domain'    => $this->captureLandingDomain(),
                'landing_page'      => $this->captureLandingPage(),
                'landing_params'    => $this->captureLandingParams(),
                'referral'          => $this->captureReferral(),
                'gclid'             => $this->captureGCLID(),
            ],
            $this->captureCampaign(),
            $this->captureReferrer(),
            $this->getCustomParameter()
        );

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
    protected function captureCampaign()
    {
        $campaigns = [];

        foreach ($this->getCampaignParameters() as $defaultKey => $campaignKeys) {
            $campaignKeys = preg_split('/[\s,]+/', $campaignKeys);
            $campaignValues = [];

            foreach ($campaignKeys as $campaignKey) {
                $campaignKey = trim($campaignKey);

                if ($this->request->has($campaignKey)) {
                    $campaignValues[$campaignKey] = $this->request->input($campaignKey);
                }
            }
            
            $campaigns[$defaultKey] = count($campaignValues) === 1 ? reset($campaignValues) : (count($campaignValues) > 1 ? $campaignValues : null);
            
            // $campaigns[$defaultKey] = count($campaignValues) ? $campaignValues : null;
            // $campaigns[$defaultKey] = count($campaignValues) > 1 ? $campaignValues : reset($campaignValues);
        }

        return $campaigns;
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
