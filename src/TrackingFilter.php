<?php

namespace MasudZaman\Trails;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Jaybizzle\CrawlerDetect\CrawlerDetect;
use Illuminate\Support\Str;
use MasudZaman\Trails\CommonTrait;

class TrackingFilter implements TrackingFilterInterface
{
    use CommonTrait;

    /**
     * The Request instance.
     *
     * @var \Illuminate\Http\Request
     */
    protected Request $request;

    /**
     * Determine whether or not the request should be tracked.
     *
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    public function shouldTrack(Request $request): bool
    {
        $this->request = $request;
        
        //Only track supported request methods
        $supportedMethods = ['get', 'post', 'put'];
        if (!in_array(strtolower($this->request->method()), $supportedMethods)) {
            return false;
        }
        
        if ($this->disableOnAuthentication()) {
            return false;
        }
        
        if ($this->disabledLandingPages($this->captureLandingPage())) {
            return false;
        }
        
        if ($this->disableInternalLinks()) {
            return false;
        }
        
        if ($this->disableRobotsTracking()) {
            return false;
        }
        
        return true;
    }

    /**
     * @return bool
     */
    protected function disableOnAuthentication()
    {
        if (Auth::guard(config('trails.guard'))->check() && config('trails.disable_on_authentication')) {
            return true;
        }
    }

    /**
     * @return bool
     */
    protected function disableInternalLinks()
    {
        if (! config('trails.disable_internal_links')) {
            return false;
        }
        
        if ($trail = $this->request->trail()) {
        
            if( Visit::campaigns($trail)->count() > 0) {
                return false;
            }
            if($this->urlHasCampaign()) {
                return false;
            }
        }
        
        if (Auth::user()?->campaigns) {
            return false;
        }
        
        if($this->request->has('ref')) {
            return false;
        }
        
        if ($referrer_domain = $this->request->headers->get('referer')) {
            $referrer_domain = parse_url($referrer_domain)['host'] ?? null;
            $request_domain  = $this->request->server('SERVER_NAME');
            if ($referrer_domain && ($referrer_domain === $request_domain)) {
                return true;
            }
        }

        return false;
    }

    /**
     *
     * @param   string  $landing_page
     * @return  array|boolean
     */
    protected function disabledLandingPages($landing_page = null)
    {
        $blacklist = (array) config('trails.landing_page_blacklist');
        if ($landing_page) {
            foreach ($blacklist as $pattern) {
                if (\Str::is($pattern, $landing_page)) {
                    return true;
                }
            }
            return false;
        } else {
            return $blacklist;
        }
    }

    /**
     * @return string
     */
    protected function captureLandingPage()
    {
        return $this->request->path();
    }

    /**
     * @return bool
     */
    protected function disableRobotsTracking()
    {
        if (! config('trails.disable_robots_tracking')) {
            return false;
        }
        return (new CrawlerDetect)->isCrawler();
    }
}
