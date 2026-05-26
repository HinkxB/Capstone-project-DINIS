<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $table = 'location'; // Check if your table is 'locations' or 'location'
    protected $primaryKey = 'location_id';
    public $timestamps = false;
    protected $fillable = ['name', 'location_type'];
}