<?php

namespace App\Models\Managers;


abstract class BaseManager {

    protected $entity;
    protected $data;

    public function __construct($entity, $data)
    {
        $this->entity = $entity;
        $this->data   = array_only($data, array_keys($this->getRules()));
    }

    abstract public function getRules();

    public function isValid()
    {
        $rules = $this->getRules();

        $validation = \Validator::make($this->data, $rules);

        if ($validation->fails())
        {
            //var_dump($validation->messages());
            //throw new ValidationException('Validation failed', $validation->messages());
            $this->entity->errors = $validation->messages();
            return false;
        }
        
        return true;
    }

    public function prepareData($data)
    {
        return $data;
    }

    public function save()
    {
        if($this->isValid())
        {
            $this->entity->fill($this->prepareData($this->data));
            $this->entity->save();
            return true;
        }
        return false;
    }

}
