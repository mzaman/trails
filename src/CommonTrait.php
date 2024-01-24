<?php

namespace MasudZaman\Trails;

use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use MasudZaman\Trails\Constants\ActionTypes;

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

        // Filter columns that start with "utm_"
        $campaignColumns = array_filter($columns, function ($column) {
            return strpos($column, config('trails.campaign_prefix')) === 0;
        });

        return array_values($campaignColumns);
    }

    /**
     * @return string|null
     */
    protected function captureActionType()
    {
        // Use the determineActionType method to calculate action_type
        return $this->determineActionType();
    }

    /**
     * @return string|null
     */
    protected function determineActionType()
    {

        // Default to null for unrecognized conditions
        return $this->request->input('action_type') ?? ActionTypes::VIEW;
    }

    /**
     * @return mixed
     */
    protected function captureActionValue()
    {
        $actionType = $this->captureActionType();

        switch ($actionType) {
            case 'conversion':
                // Example: For conversion, use the purchase amount as action_value
                return $this->request->input('purchase_amount');

            case 'rating':
                // Example: For rating, use the user's rating as action_value
                return $this->request->input('user_rating');

            // Add additional cases for other action types and their associated values
            default:
                // Default to null for unrecognized action types
                return null;
        }
    }


}
