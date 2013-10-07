<?php namespace Codeaqua\Controllers;

use Codeaqua\Response as CAQResponse;

use Codeaqua\Models\User;
use Codeaqua\Models\Party;
use Codeaqua\Models\PartyUser;

class PartiesUsersController extends BaseController {

    public function __construct(User $users,
                                Party $party,
                                PartyUser $partyUser)
    {
        $this->beforeFilter('api-auth');

        $this->users = $users;
        $this->party = $party;
        $this->partyUser = $partyUser;
    }

    public function edit($partyId, $userId)
    {
        if(\Input::has('action') === false || empty(\Input::get('action')) === true ) {
            return CAQResponse::error(['You need to provide an action to perform. (available actions: make_officer, make_hoster, make_member)']);
        }
        $partyUser = $this->partyUser->edit($partyId, $userId, \Input::get('action'));
        if($partyUser) {
            return CAQResponse::json(null, 200, 'Resource updated successfully.');
        }
        return CAQResponse::error(['There has been an error.']);
    }

    public function join($id)
    {
        $partyUser = $this->partyUser->joinParty($id);
        if($partyUser) {
            return CAQResponse::json(null, 200, 'User has joined to the party successfully');
        }
        return CAQResponse::error(['There has been an when trying to make user join.']);
    }

    public function unjoin($id)
    {
        $partyUser = $this->partyUser->unjoinParty($id);
        if($partyUser) {
            return CAQResponse::json(null, 200, 'User has unjoined to the party successfully');
        }
        return CAQResponse::error(['There has been an when trying to make user unjoin.']);
    }

    public function checkin($id)
    {
        $partyUser = $this->partyUser->checkin($id);
        if($partyUser) {
            return CAQResponse::json(null, 200, 'User has checkedin to the party successfully');
        }
        return CAQResponse::error(['There has been an when trying to check user in.']);
    }

    public function checkout($id)
    {
        $partyUser = $this->partyUser->checkout($id);
        if($partyUser) {
            return CAQResponse::json(null, 200, 'User has checkedout to the party successfully');
        }
        return CAQResponse::error(['There has been an when trying to check user out.']);
    }

    public function joining($id)
    {
        $userList = $this->partyUser->joining($id);
        if(count($userList) > 0) {
            return CAQResponse::json($userList);
        }
        return CAQResponse::error(['Requested Resource not found'], 404);
    }

    public function checkins($id)
    {
        $userList = $this->partyUser->checkins($id);
        if(count($userList) > 0) {
            return CAQResponse::json($userList);
        }
        return CAQResponse::error(['Requested Resource not found'], 404);
    }
}
