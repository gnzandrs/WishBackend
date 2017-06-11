<?php

namespace App\Models\Entities;

use Illuminate\Database\Eloquent\Model;

class Configuration extends Model
{
    protected $table = 'configuration';
    protected $fillable = array('notificacion', 'deal');

    public function user()
    {
        return $this->belongsTo('App\Models\Entities\User', 'user_id', 'id');
    }
}
