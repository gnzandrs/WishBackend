<?php

namespace App\Models\Entities;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $table = 'city';
    protected $fillable = ['name','code'];

    public function country()
    {
        return $this->belongsTo('App\Models\Entities\Country', 'country_id','id');
    }
}
