<?php namespace Codeaqua\Controllers;

use Codeaqua\Models\GroupUser;
use Codeaqua\Models\User;

use Codeaqua\Response as CAQResponse;

class GroupsUsersController extends BaseController {

    public function __construct(User $users,
                                GroupUser $groupUser)
    {
        $this->beforeFilter('api-auth');

        $this->users = $users;
        $this->groupUser = $groupUser;
    }

    /**
     * API call to make logged user join
     * to the specified group
     * @param  integer $id Group's id.
     * @return Codeaqua\Response
     */
    public function join($id)
    {
        $groupUser = new GroupUser;
        $groupUser->groupId = $id;
        $groupUser->userId = \Auth::user()->id;
        $groupUser->groupRoleId = GroupUser::$roles['member'];

        if($groupUser->save()) {
            $groupUser->load('group', 'user', 'groupRole');
            return CAQResponse::json($groupUser, 201, 'You have successfully joined the group.');
        }
        return CAQResponse::error($groupUser->getErrors());
    }

    /**
     * Change the relationship between a user
     * and a group. Such as promoting him/her to
     * officer, or downgrading him/her to user again.
     * @param  integer $groupId Group's id.
     * @param  integer $userId  The user's id whom relationship is being edited.
     * @return Codeaqua\Response
     */
    public function editMembership($groupId, $userId)
    {
        if(!\Input::has('action')) {
            return CAQResponse::error(['You need to provide an action to perform']);
        }

        $action = \Input::get('action');

        $groupUser = GroupUser::initFromDatabase($groupId, $userId);

        switch ($action) {
            case 'promote':
                if($groupUser->promote()) {
                    $groupUser->load('user', 'group', 'groupRole');
                    return CAQResponse::json($groupUser, 200, 'User promoted successfully');
                }
                return CAQResponse::error('User needs to be a member to be promoted.');
            break;
            case 'demote':
                if($groupUser->demote()) {
                    $groupUser->load('user', 'group', 'groupRole');
                    return CAQResponse::json($groupUser, 200, 'User promoted successfully');
                }
                return CAQResponse::error('User needs to be at least an officer to be demoted.');
            break;
            
            default:
                return CAQResponse::error(['Please supply an applicable action type. (action types: promote)'], 409);
            break;
        }
    }

    public function users($groupId = null)
    {
        if($groupId === null) {
            return CAQResponse::error(['You need to provide a group id.']);
        }

        $groupsUsers = $this->groupUser->with('user', 'group', 'groupRole')->where('groupId', '=', $groupId)->get();
        return CAQResponse::json($groupsUsers);
    }
};