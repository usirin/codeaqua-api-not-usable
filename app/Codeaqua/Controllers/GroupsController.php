<?php namespace Codeaqua\Controllers;

use Codeaqua\Models\Group;
use Codeaqua\Models\User;
use Codeaqua\Models\GroupUser;

use Codeaqua\Response as CAQResponse;

class GroupsController extends BaseController {

    public function __construct(Group $groups,
                                User $users)
    {
        $this->beforeFilter('api-auth');

        $this->groups = $groups;
        $this->users = $users;
    }

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
        $groups = $this->groups->get();
        if(count($groups) === 0) {
            return CAQResponse::error(['Resource not found'], 404);
        }
        return CAQResponse::json($groups);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
        $group = new Group(\Input::all());
        if($group->save()) {
            $groupUser = new GroupUser(['userId' => \Auth::user()->id, 'groupId' => $group->id, 'groupRoleId' => GroupUser::$roles['owner']]);
            $groupUser->save();
            return CAQResponse::json($group, 201, 'New group has been created.');
        }
        return CAQResponse::error($group->getErrors());
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
	    $group = $this->groups->find($id);
        if(!$group) {
            return CAQResponse::error(['Resource not found.'], 404);
        }
        return CAQResponse::json($group);
	}

}