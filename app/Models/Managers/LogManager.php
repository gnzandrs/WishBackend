<?php namespace Wish\Managers;

class LogManager extends BaseManager {

    public function getRules()
    {
        $rules = [
            'file'          => 'required',
            'class'         => 'required',
            'description'   => 'required',
            'exception'     => 'required'
        ];

        return $rules;
    }

}