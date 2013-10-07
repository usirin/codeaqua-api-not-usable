<?php namespace Codeaqua\Controllers;

use Codeaqua\Response as CAQResponse;
use Codeaqua\Models\Location;

class LocationsController extends BaseController {

    public function __construct(Location $locations)
    {
        // $this->beforeFilter('api-auth');

        $this->locations = $locations;
    }

    public function index()
    {
        $locations = $this->locations->with('user')->get();
        return CAQResponse::json($locations);
    }

    public function store()
    {
        $location = new Location(\Input::all());
        $location->userId = \Auth::user()->id;
        if($location->save()) {
            return CAQResponse::json($location, 201, 'New location has been created.');
        }
        return CAQResponse::error($location->getErrors());
    }

    public function show($id)
    {
        return CAQResponse::json($this->locations->with('user')->find($id));
    }

    public function near()
    {
        return $this->index();
    }


}