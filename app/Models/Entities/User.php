<?php

namespace App\Models\Entities;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'user';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'name', 'lastname', 'email', 'genre', 'city_id', 'password'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token'
    ];

    public $errors;

    public function configuration()
    {
        return $this->hasOne('App\Models\Entities\Configuration', 'user_id', 'id');
    }

    public function city()
    {
        return $this->hasOne('App\Models\Entities\City', 'id', 'city_id');
    }

    public function setPasswordAttribute($value)
    {
        if (!empty($value))
        {
            $this->attributes['password'] = \Hash::make($value);
        }
    }

    public function lists()
    {
        return $this->hasMany('App\Models\Entities\WishList');
    }

    public function UserImage()
    {
        return $this->hasOne('App\Models\Entities\UserImage', 'user_id', 'id');
    }
}
