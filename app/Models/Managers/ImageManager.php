<?php namespace Wish\Managers;

class ImageManager extends BaseManager {

    public function getRules()
    {
        $rules = [
            'path'          => 'required',
            'thumb_path'    => 'required',
            'wish_id'       => 'required'
        ];

        return $rules;
    }
} 