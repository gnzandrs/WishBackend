<?php

namespace App\Models\Entities;

use Illuminate\Database\Eloquent\Model;

class Wish extends Model
{
    protected $table = 'wish';
    protected $fillable = ['description','reference','price', 'list_id', 'location_id', 'category_id'];
    protected $perPage = 3;

    public function wishlist()
    {
        return $this->belongsTo('App\Models\Entities\WishList', 'list_id','id');
    }

    public function location()
    {
        return $this->belongsTo('App\Models\Entities\Location');
    }

    public function wishimages()
    {
        return $this->hasMany('App\Models\Entities\WishImage', 'wish_id', 'id');
    }

    public function wishstatus()
    {
        return $this->hasOne('App\Models\Entities\WishStatus', 'wish_id', 'id');
    }

    public function category()
    {
        return $this->hasOne('App\Models\Entities\Category', 'id', 'category_id');
    }
}
