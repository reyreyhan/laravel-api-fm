<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Absent extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'long', 'lat', 'user_id',
    ];
}
