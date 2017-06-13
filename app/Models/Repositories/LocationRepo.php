<?php

namespace Wish\Repositories;

use App\Models\Entities\Location;

class LocationRepo extends BaseRepo {

    public function getModel()
    {
        return new Location;
    }

    public function newLocation()
    {
        $location = new Location();
        return $location;
    }

    public function search($lat, $lng)
    {
        return Location::where('latitude', '=', $lat)->where('longitude', '=', $lng)->get();
    }

    public function getRecent()
    {

    }

    public function getMarkers()
    {
        return Location::all();
    }
} 