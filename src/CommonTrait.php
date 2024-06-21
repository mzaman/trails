<?php

namespace MasudZaman\Trails;

use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

/**
 * Trait CommonTrait.
 */
trait CommonTrait
{

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
     * @return bool
     */
    protected function urlHasCampaign($url = null) {
        $url = $url ?? request()->url();
        return count(array_filter($this->captureCampaign())) > 1 ? true : false;
    }

    /**
     * Get the campaign parameters with modified keys.
     *
     * This method combines the modified campaign keys with the original parameter values.
     *
     * @return array The associative array with modified keys and original values.
     */
    protected function getCampaignParameters()
    {
        return array_combine($this->getCampaignKeys(), config('trails.campaign_parameters'));
    }

    /**
     * Get the modified campaign keys.
     *
     * This method adds the campaign prefix to each key in the original campaign parameters array.
     *
     * @return array The array containing modified campaign keys.
     */
    protected function getCampaignKeys()
    {
        return array_map(fn($key) => config('trails.campaign_prefix') . $key, array_keys(config('trails.campaign_parameters')));
    }


    /**
     * Get columns of Visit model that start with "utm_".
     *
     * @return array
     */
    protected function campaignColumns()
    {
        $table = (new Visit)->getTable();
        $columns = Schema::getColumnListing($table);

        // Filter columns that start with campaign_prefix
        $campaignColumns = array_filter($columns, function ($column) {
            return strpos($column, config('trails.campaign_prefix')) === 0;
        });

        return array_values($campaignColumns);
    }

}
