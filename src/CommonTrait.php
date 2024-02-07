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
