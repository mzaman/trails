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
     * Get columns of Visit model that start with "utm_".
     *
     * @return array
     */
    public function utmColumns()
    {
        $table = (new Visit)->getTable();
        $columns = Schema::getColumnListing($table);

        // Filter columns that start with "utm_"
        $utmColumns = array_filter($columns, function ($column) {
            return strpos($column, 'utm_') === 0;
        });

        return array_values($utmColumns);
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
