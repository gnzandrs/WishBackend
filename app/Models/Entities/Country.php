<?php

namespace App\Models\Entities;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $table = 'country';
    protected $fillable = ['name','code'];

    public function city()
    {
        return $this->hasMany('App\Models\Entities\City', 'country_id', 'id');
    }
}
