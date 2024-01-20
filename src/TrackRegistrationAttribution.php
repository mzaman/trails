<?php

namespace MasudZaman\Trails;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;
use MasudZaman\Trails\Jobs\AssignPreviousVisits;

/**
 * Class TrackRegistrationAttribution.
 *
 * @method static void created(callable $callback)
 */
trait TrackRegistrationAttribution
{
    public static function bootTrackRegistrationAttribution()
    {
        // Add an observer that upon registration will automatically sync up prior visits.
        static::created(function (Model $model) {
            $model->trackRegistration(request());
        });
    }

    /**
     * Get all of the visits for the user.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function visits()
    {
        return $this->hasMany(Visit::class, config('trails.column_name'))->orderBy('created_at', 'desc');
    }

    /**
     * Method depricated use 'trackRegistration' method.
     *
     * @deprecated
     * @return void
     */
    public function assignPreviousVisits()
    {
        return $this->trackRegistration();
    }

    /**
     * Assign earlier visits using current request.
     */
    public function trackRegistration(Request $request): void
    {
        $job = new AssignPreviousVisits($request->trail(), $this);

        if (config('trails.async') == true) {
            dispatch($job);
        } else {
            $job->handle();
        }
    }

    /**
     * The initial attribution data that eventually led to a registration.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function initialAttributionData()
    {
        return $this->hasMany(Visit::class, config('trails.column_name'))->orderBy('created_at', 'asc')->first();
    }

    /**
     * The final attribution data before registration.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function finalAttributionData()
    {
        return $this->hasMany(Visit::class, config('trails.column_name'))->orderBy('created_at', 'desc')->first();
    }
    
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
     * Retrieve UTM data from visits with specified conditions.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function utms()
    {
        // Build the query to retrieve UTM data
        return $this->hasMany(Visit::class, config('trails.column_name'))
            ->whereNotNull($this->utmColumns()) // Add whereNotNull conditions for UTM columns
            ->orderBy('created_at', 'asc');
    }

}
