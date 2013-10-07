<?php namespace Codeaqua\Controllers;

use Codeaqua\Models\User;
use Codeaqua\Models\PartyUser;
use Codeaqua\Models\Party;
use Codeaqua\Models\PartyUserVote;

use Codeaqua\Response as CAQResponse;

class PartiesVotesController extends BaseController {
    
    public function __construct(User $users, PartyUser $partyUser, Party $parties, PartyUserVote $votes)
    {
        $this->users = $users;
        $this->partyUser = $partyUser;
        $this->parties = $parties;
        $this->votes = $votes;
        // $this->beforeFilter('api-auth');
    }

    public function index($id)
    {
        $partyUserIdList = $this->partyUser->where('partyId', '=', $id)->lists('id');
        $votes = $this->votes->whereIn('partyUserId', $partyUserIdList)->get();
        return CAQResponse::json($votes);
    }

    public function vote($id)
    {
        $vote = new PartyUserVote(\Input::all());
        if($vote->vote($id)) {
            $vote->party = $this->parties->find($id)->toArray();
            $vote->user = \Auth::user()->toArray();
            $vote['resourceType'] = 'votes';
            return CAQResponse::json($vote, 201, 'Your vote has been recorded.');
        }
        return CAQResponse::error($vote->getErrors());
    }

    public function userVotes($partyId, $userId)
    {
        $userVotes = \DB::table('partyUserVotes')
                        ->join('party_user', function($join) {
                            $join->on('partyUserVotes.partyUserId', '=', 'party_user.id');
                        })
                        ->where('party_user.userId', '=', $userId)
                        ->where('party_user.partyId', '=', $partyId)
                        ->select('party_user.userId', 'party_user.partyId', 'partyUserVotes.vote', 'partyUserVotes.createdAt')
                        ->get();
        $userVotes['resourceType'] = 'votes';
        return CAQResponse::jso
    }

}