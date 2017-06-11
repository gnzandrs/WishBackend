<?php

namespace App\Models\Entities;

use Illuminate\Database\Eloquent\Model;

class WishList extends Model
{
    protected $table = 'list';
    protected $fillable = ['name'];

    public function user()
    {
        return $this->belongsTo('App\Models\Entities\User', 'user_id', 'id');
    }

    public function wishs()
    {
        return $this->hasMany('App\Models\Entities\Wish', 'list_id', 'id');
    }

    public function getPaginateWishsAttribute()
    {
        return  Wish::where('list_id', $this->id)->paginate();
    }
}
