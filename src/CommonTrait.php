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
     * Capture campaign parameters from the request.
     *
     * This method retrieves campaign parameters based on predefined keys
     * and their aliases, then filters and formats them into an associative array.
     *
     * @return array An associative array of captured campaign parameters.
     */
    protected function captureCampaign()
    {
        $campaigns = [];

        // Iterate through the campaign parameters
        foreach ($this->getCampaignParameters() as $defaultKey => $campaignKeys) {
            // Split campaign keys by spaces or commas
            $campaignKeys = preg_split('/[\s,]+/', $campaignKeys);
            $campaignValues = [];

            // Check for the presence of each campaign key in the request
            foreach ($campaignKeys as $campaignKey) {
                $campaignKey = trim($campaignKey);

                if (request()->has($campaignKey)) {
                    $campaignValues[$campaignKey] = request()->input($campaignKey);
                }
            }

            // Determine the value to store for each campaign parameter
            if (count($campaignValues) === 1) {
                $campaigns[$defaultKey] = reset($campaignValues);
            } elseif (count($campaignValues) > 1) {
                $campaigns[$defaultKey] = $campaignValues;
            } else {
                $campaigns[$defaultKey] = null;
            }
        }

        return $campaigns;
    }

    /**
     * Check if the URL has campaign parameters.
     *
     * This method verifies if there are multiple campaign parameters present in the given URL.
     *
     * @return bool True if the URL has more than one campaign parameter, false otherwise.
     */
    protected function urlHasCampaign()
    {
        return count(array_filter($this->captureCampaign())) > 1;
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
     * This method retrieves the columns of the Visit model that start with the campaign prefix.
     *
     * @return array An array of column names that start with the campaign prefix.
     */
    protected function campaignColumns()
    {
        $table = (new Visit)->getTable();
        $columns = Schema::getColumnListing($table);

        // Filter columns that start with the campaign prefix
        $campaignColumns = array_filter($columns, function ($column) {
            return strpos($column, config('trails.campaign_prefix')) === 0;
        });

        return array_values($campaignColumns);
    }
}