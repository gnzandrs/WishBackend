<?php namespace App\Models\Managers;

class RegisterManager extends BaseManager {

    public function getRules()
    {
        $rules = [
            'username'              => 'required|unique:user',
            'name'                  => 'required',
            'lastname'              => 'required',
            'email'                 => 'required|email|unique:user,email',
            'genre'                 => 'required',
            //country
            'city_id'               => 'required',
            'password'              => 'required|confirmed',
            'password_confirmation' => 'required'
        ];

        return $rules;
    }


}
