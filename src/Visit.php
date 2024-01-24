<?php

namespace MasudZaman\Trails;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use MasudZaman\Trails\CommonTrait;

class Visit extends Model
{
    use CommonTrait;

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

        $this->setTable(config('trails.table_name'));

        if (config('trails.connection_name')) {
            $this->setConnection(config('trails.connection_name'));
        }
    }

    /**
     * Get the account that owns the visit.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account()
    {
        $model = config('trails.model');

        return $this->belongsTo($model, config('trails.column_name'));
    }

    /**
     * Scope a query to only include previous visits.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePreviousVisits($query, $trail)
    {
        return $query->where('trail', $trail);
    }

    /**
     * Scope a query to only include previous visits that have been unassigned.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUnassignedPreviousVisits($query, $trail)
    {
        return $query->whereNull(config('trails.column_name'))->where('trail', $trail);
    }

    /**
     * Scope a query to only include campaign visits.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCampaigns($query, $trail = null)
    {
        $query = $query->whereNotNull($this->getCampaignKeys());

        return $trail ? $query->where('trail', $trail) : $query;
    }

    /**
     * Scope a query to only include unassigned visits older than $days days
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePrunable($query, $days)
    {
        return $query->whereNull(config('trails.column_name'))->where('created_at', '<=', today()->subDays($days));
    }
}
