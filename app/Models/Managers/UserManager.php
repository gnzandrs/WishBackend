<?php namespace App\Models\Managers;

class UserManager extends BaseManager {

    public function getRules()
    {
        $rules = [
            'name'                  => 'required',
            'lastname'              => 'required',
            //'email'                 => 'required|email|unique:user,email' . $this->entity->id
            'genre'                 => 'required',
            'city_id'               => 'required'
        ];

        return $rules;
    }


} 