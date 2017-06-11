<?php

namespace App\Models\Entities;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'category';
    protected $fillable = ['name'];

    public function wishs()
    {
        return $this->hasMany('App\Models\Entities\Wish', 'category_id', 'id');
    }
}
