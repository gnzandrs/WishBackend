<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Entities\Location;
use App\Models\Managers\LocationManager;
use App\Models\Repositories\LocationRepo;
use App\Models\Repositories\WishRepo;

class LocationController extends Controller
{
    protected $locationRepo;
    protected $wishRepo;

    public function __construct(LocationRepo $locationRepo, WishRepo $wishRepo)
    {
      $this->locationRepo = $locationRepo;
      $this->wishRepo = $wishRepo;
    }

    // return index default view
    public function index()
    {
      try {
          return dd('hellow :)');
      }
      catch (Exception $e)
      {
          Log::error('LocationController index: '.$e);
          $this->logRepo->newLog('LocationController.php', 'LocationController.php', 'error catch', $e);
          return 0;
      }
    }

    // return the location for a id
    public function getLocation()
    {
      try {
          $id = Input::get("id");
          $location = $this->locationRepo->find($id);
          if (Request::ajax())
          {
              return Response::json($location);
          }
      }
      catch (Exception $e)
      {
          Log::error('LocationController getLocation(): '.$e);
          $this->logRepo->newLog('LocationController.php', 'LocationController.php', 'error catch', $e);
          return 0;
      }
    }

    // return the location for a specific wish
    public function getLocationByWish()
    {
      try {
          $wishId = Input::get("id");
          $wish = $this->wishRepo->find($wishId);
          $location = $this->locationRepo->find($wish->location_id);

          if (Request::ajax())
          {
              return Response::json($location);
          }
      }
      catch (Exception $e)
      {
          Log::error('LocationController getLocation(): '.$e);
          $this->logRepo->newLog('LocationController.php', 'LocationController.php', 'error catch', $e);
          return 0;
      }
    }

    // get all the markers from the db
    public function getMarkers()
    {
      try {
          $locations = $this->locationRepo->getMarkers();

          if (Request::ajax())
          {
              return Response::json($locations);
          }
      }
      catch (Exception $e)
      {
          Log::error('LocationController getMarkers(): '.$e);
          $this->logRepo->newLog('LocationController.php', 'LocationController.php', 'error catch', $e);
          return 0;
      }
    }

    // return the last location added to the db
    public function getRecentLocations()
    {
      try {
          $locations = $this->locationRepo->getMarkers();
          if (Request::ajax())
          {
              return Response::json($locations);
          }
      }
      catch (Exception $e)
      {
          Log::error('LocationController getRecentLocations(): '.$e);
          $this->logRepo->newLog('LocationController.php', 'LocationController.php', 'error catch', $e);
          return 0;
      }
    }

    // search a location by latitud and longitud parameters
    public function search($lat, $lng)
    {
      try {
          $location = $this->locationRepo->search($lat,$lng);
          if($location->count() == 0)
          {
              return View::make('location/location', array('location' => $location));
          }
          else{
              return View::make('location/show', array('location' => $location));
          }
      }
      catch (Exception $e)
      {
          Log::error('LocationController search($lat, $lng): '.$e);
          $this->logRepo->newLog('LocationController.php', 'LocationController.php', 'error catch', $e);
          return 0;
      }
    }

    // return the location view from an ubication id
    public function show($id_ubicacion)
    {
      try {
          $location = $this->locationRepo->find($id_ubicacion);
          if (is_null($location)) App::abort(404);
          return View::make('location/show', array('location' => $location));
      }
      catch (Exception $e)
      {
          Log::error('LocationController show($id_ubicacion): '.$e);
          $this->logRepo->newLog('LocationController.php', 'LocationController.php', 'error catch', $e);
          return 0;
      }
    }

    // store a new location
    public function store()
    {
      try {
          $location = $this->locationRepo->newLocation();
          $data = Input::all();

          $manager = new LocationManager($location, Input::all());
          if($manager->save())
          {
              if (Request::ajax())
              {
                  return $location->id;
              }
          }
          else{
              return 0;
          }
      }
      catch (Exception $e)
      {
          Log::error('LocationController store(): '.$e);
          $this->logRepo->newLog('LocationController.php', 'LocationController.php', 'error catch', $e);
          return 0;
      }
    }
}
