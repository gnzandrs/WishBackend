<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Entities\Location;
use App\Models\Managers\LocationManager;
use App\Models\Repositories\LocationRepo;
use App\Models\Repositories\WishRepo;
use JWTAuth;

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

    /**
     * Get all the markers from the db
     * @param  string $lat
     * @param  string  $lng
     * @return \app\Models\Entities\Location $location
     */
    public function getMarkers()
    {
      try {
          return  $this->locationRepo->getMarkers();
      }
      catch (Exception $e)
      {
         //Log::error('LocationController getMarkers(): '.$e);
         // $this->logRepo->newLog('LocationController.php', 'LocationController.php', 'error catch', $e);
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

    /**
     * Search a location by latitud and longitud parameters
     * @param  string $lat
     * @param  string  $lng
     * @return \app\Models\Entities\Location $location
     */
    public function search($lat, $lng)
    {
      try {
          return  $this->locationRepo->search($lat,$lng);
      }
      catch (Exception $e)
      {
          //Log::error('LocationController search($lat, $lng): '.$e);
          //$this->logRepo->newLog('LocationController.php', 'LocationController.php', 'error catch', $e);
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

    /**
     * Store a new location
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response $response
     */
    public function store(Request $request)
    {
      try {
          $input = $request->all();
          $userId = JWTAuth::toUser($input['token'])->id;

          $location = $this->locationRepo->newLocation();
          $manager = new LocationManager($location, $request->input('location'));
          $result = $manager->save();

          if ($result)
          {
              return [  'created' => true,
                        'id' => $location->id ];
          } else {
              return response()->json($location->errors);
          }
      }
      catch (Exception $e)
      {
          //Log::error('LocationController store(): '.$e);
          //$this->logRepo->newLog('LocationController.php', 'LocationController.php', 'error catch', $e);
          return 0;
      }
    }
}
