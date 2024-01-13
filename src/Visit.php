<?php

namespace MasudZaman\Fingerprints;

use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{

    /**
     * The name of the database table.
     *
     * @var string
     */
    protected $table = 'visits';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    /**
     * Override constructor to set the table name @ time of instantiation.
     *
     * @param array $attributes
     * @return void
     */

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('fingerprints.table_name'));

        if (config('fingerprints.connection_name')) {
            $this->setConnection(config('fingerprints.connection_name'));
        }
    }

    /**
     * Get the account that owns the visit.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account()
    {
        $model = config('fingerprints.model');

        return $this->belongsTo($model, config('fingerprints.column_name'));
    }

    /**
     * Scope a query to only include previous visits.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePreviousVisits($query, $fingerprint)
    {
        return $query->where('fingerprint', $fingerprint);
    }

    /**
     * Scope a query to only include previous visits that have been unassigned.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUnassignedPreviousVisits($query, $fingerprint)
    {
        return $query->whereNull(config('fingerprints.column_name'))->where('fingerprint', $fingerprint);
    }

    /**
     * Scope a query to only include unassigned visits older than $days days
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePrunable($query, $days)
    {
        return $query->whereNull(config('fingerprints.column_name'))->where('created_at', '<=', today()->subDays($days));
    }
}
