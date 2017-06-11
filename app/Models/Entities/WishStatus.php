<?php

namespace App\Models\Entities;

use Illuminate\Database\Eloquent\Model;

class WishStatus extends Model
{
    protected $table = 'wish_status';
    protected $fillable = ['wish_id', 'user_taken', 'status', 'date'];

    public function __construct()
    {
        //$this->wish_id = $wishId;
        //$this->status = $userStatus;
        //$this->date = date("Ymd");
    }

    public function wish()
    {
        return $this->belongsTo('App\Models\Entities\Wish', 'wish_id', 'id');
    }

    public function userTaken()
    {
        return $this->hasOne('App\Models\Entities\User', 'user_taken', 'id');
    }
}
