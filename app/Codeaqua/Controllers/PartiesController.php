<?php namespace Codeaqua\Controllers;

use Codeaqua\Response as CAQResponse;
use Codeaqua\Models\Party;

class PartiesController extends BaseController {

    public function __construct(Party $parties)
    {
        $this->beforeFilter('api-auth');

        $this->parties = $parties;
    }

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
        $parties = $this->parties->get();
        if(empty($parties)) {
            return CAQResponse::error(['Requested resource not found.'], 404);
        }
        $parties->load('hoster');
        return CAQResponse::json($parties);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
        $input = \Input::all();
        $input['hosterType'] = 'User';
        $input['hosterId'] = \Auth::user()->id;
        $party = new Party($input);
        if($party->save()) {
            $party->load($party->getWithArray());
            return CAQResponse::json($party, 201, 'New Party has been created.');
        }
        return CAQResponse::error($party->getErrors());
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
        $party = $this->parties->find($id);
        if($party) {
            return CAQResponse::json($party);
        }
        return CAQResponse::error(['Resource not found.'], 404);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
        $party = $this->parties->find($id);
        $party->load('hoster');
        foreach (\Input::all() as $key => $value) {
            $party->$key = $value;
        }

        if($party->save()) {
            CAQResponse::json(null, 204, 'Resource updated successfully.');
        }
        return CAQResponse::error($party->getErrors());
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
        
	}

}