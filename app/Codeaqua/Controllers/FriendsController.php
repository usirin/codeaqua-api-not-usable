<?php namespace Codeaqua\Controllers;

use Codeaqua\Models\User;
use Codeaqua\Models\Friendship;
use Codeaqua\Response as CAQResponse;

class FriendsController extends BaseController {

    /**
     * Positive action name. It is being used
     * for adding two friends.
     *
     * @var string
     */
    const ACTION_POSITIVE = 'confirm';

    /**
     * Negative action name. It is being used
     * for changing the relationship between users
     * in a negative way.
     *
     * @var string
     */
    const ACTION_NEGATIVE = 'deny';

    public function __construct(User $users, Friendship $friendship)
    {
        $this->users = $users;
        $this->friendship = $friendship;
        $this->beforeFilter('api-auth');
    }

    /**
     * Return the friends of the requested
     * user.
     * @param  integer $id Requested User's id.
     * @return Codeaqua\Response
     */
    public function getIndex($id)
    {
        if(is_numeric($id)) {
            $user = $this->users->find($id);
        }
        else if(is_string($id) && $id === 'self') {
            $user = \Auth::user();
        }
        else if(is_string($id)) {
            $user = $this->users->where('username', '=', $id)->first();
        }

        $friends = $user->friends();
        $friends['resourceType'] = 'friends';
        if($friends) {
            return CAQResponse::json($friends);
        }
        return CAQResponse::error(['Requested resource is not available'], 404);
    }

    /**
     * Necessary action to create relationship
     * between logged user and the target user
     * of id $id.
     * @param  integer $id Target User's id
     * @return CAQResponse::json     Necessary json response.
     */
    public function postIndex($id)
    {
        $friendship = new Friendship;
        $friendship->targetId = $id;
        $friendship->sourceId = \Auth::user()->id;
        $friendship->status = Friendship::RELATIONSHIP_STATUS_REQUEST;
        if($friendship->save()) {
            $friendship->load('targetUser');
            return CAQResponse::json($friendship->targetUser, 201, 'Friendship request sent.');
        }
        return CAQResponse::error($friendship->getErrors());
    }

    /**
     * Necessary PUT request method to update
     * relationship between 2 users.
     * @param  integer $targetId The user has been added as friend.
     * @param  integer $sourceId The user has added target as a friend.
     * @return 
     */
    public function postUpdateFriendship($targetId, $sourceId) 
    {
        if(is_string($targetId) && $targetId === 'self') {
            $targetId = \Auth::user()->id;
        }

        if(!$friendship = $this->friendship->exists($targetId, $sourceId)) {
            return CAQResponse::error(['There is no pending request between this users']);
        }

        // dd($friendship);

        // get the input so that we can use it later.
        $input = \Input::all();

        switch ($input['action']) {
            case static::ACTION_POSITIVE:
                $friendship->status = Friendship::RELATIONSHIP_STATUS_FRIEND;
                $friendship->save();
                return CAQResponse::json(null, 200, 'Friendship request confirmed successfully.');
            break;

            case static::ACTION_NEGATIVE:
                $friendship->status = Friendship::RELATIONSHIP_STATUS_DENIED;
                $friendship->save();
                return CAQResponse::json(null, 200, 'Friendship request denied successfully.');
            break;

            default:
                return CAQResponse::error(['Please supply an applicable action type. (action types: ' . static::ACTION_POSITIVE . ', ' . static::ACTION_NEGATIVE . ')'], 409);
            break;
        }

    }

    public function getFriendshipRequests($targetId)
    {
        if(is_string($targetId) && $targetId === 'self') {
            $targetId = \Auth::user()->id;
        }

        $friendRequests = $this->friendship->with(['targetUser', 'sourceUser'])
                            ->where('targetId', '=', $targetId)
                            ->where('status', '=', Friendship::RELATIONSHIP_STATUS_REQUEST)
                            ->get();

        $friendRequests['resourceType'] = 'requests';

        return CAQResponse::json($friendRequests, 200);
    }


}