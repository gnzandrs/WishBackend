<?php

namespace App\Models\Entities;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $table = 'location';
    protected $fillable = ['name','latitude','longitude'];

    public function wishs()
    {
        return $this->hasMany('App\Models\Entities\Wish', 'location_id', 'id');
    }
}
