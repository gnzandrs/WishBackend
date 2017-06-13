<?php namespace Wish\Managers;

class OptionManager extends BaseManager {

    public function getRules()
    {
        $rules = [
            'notificacion'                  => 'required'
        ];

        return $rules;
    }


}