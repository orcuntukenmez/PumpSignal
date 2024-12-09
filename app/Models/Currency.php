<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;

    protected $table = 'currencies';

    protected $fillable = [
        'unique_id',
        'name',
        'symbol',
        'price',
        'rise_alert',
        'rise_alert_interval',
        'fall_alert',
        'fall_alert_interval',
        'is_active',
        'm1',
        'm5',
        'm15',
        'm30',
        'h1',
        'h4',
        'h12',
        'd1',
        'created_at',
        'updated_at',
    ];

    public $timestamps = true;
}
