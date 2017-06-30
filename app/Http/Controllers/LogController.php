<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Entities\Log;
use App\Models\Managers\LogManager;
use App\Models\Repositories\LogRepo;

class LogController extends Controller
{
    protected $logRepo;

    public function __construct(LogRepo $logRepo)
    {
      $this->logRepo = $logRepo;
    }

    // redirect to index
    public function index()
    {
      return Redirect::route('home');
    }

    // save log into database
    public function store()
    {
      try {
          $log = $this->logRepo->newLog();
          $manager = new RegisterManager($log, Input::all());
          $result = $manager->save();
      }
      catch(exception $e)
      { }
    }
}
