<?php

namespace App\Models\Repositories;

use App\Models\Entities\Log;
use Illuminate\Support\Facades\BD;

class LogRepo extends BaseRepo {

    public function getModel()
    {
        return new Log;
    }

    public function newLog($file, $class, $description, $exception)
    {
        try{
            $log = new Log();
            $values = [
                'file' => $file,
                'class' => $class,
                'description' => $description,
                'exception' => $exception
            ];
            $log->fill($values);
            $log->save();
        }
        catch(Exception $e) { }

    }

}