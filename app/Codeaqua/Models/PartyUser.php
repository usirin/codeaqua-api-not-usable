<?php namespace Codeaqua\Models;

class PartyUser extends BaseModel {

    protected $table = 'party_user';

    protected $guarded = [];

    protected $with = ['joining', 'checkins', 'user'];

    public static $rules = [
        'userId'   => 'required',
        'partyId'  => 'required',
        'roleId'   => 'required',
        'statusId' => 'required'
    ];

    public static $allowedActions = [
        'make_hoster',
        'make_officer',
        'make_member'
    ];

    public static $statuses = [
        'invited'  => 1,
        'joined'   => 2,
        'unjoined' => 3,
        'checkin'  => 4,
        'checkout' => 5
    ];

    public $partyUserRoles;

    public function __construct($attributes = array(), $exists = false)
    {
        parent::__construct($attributes, $exists);
        $this->partyUserRoles = new PartyUserRole;
    }

    /**
     * Editing the party user role. Such as
     * making a user a hoster, an officer etc.
     * @param  integer $partyId
     * @param  integer $userId 
     * @param  string $action e.g make_hoster, make_officer, make_member
     * @return boolean
     */
    public function edit($partyId, $userId, $action) {
        // we will first check whether the passed
        // action type is allowed.
        if(!in_array($action, static::$allowedActions)) {
            \App::abort(400, 'Provided action is not applicable. Please provide an applicable one. (make_hoster, make_officer, make_member)');
        }

        $loggedUserPartyRelationship = static::where('partyId', '='. $partyId)->where('userId', '=', \Auth::user()->id)->first();

        // we are checking if the logged user 
        // has a relationship with the party,
        // if not, s/he can't change anything.
        if(!$loggedUserPartyRelationship) {
            \App::abort(403, "You don't have the priviliges to perform this action. (reason: User does not have a record with this party.)");
        }

        // if s/he has a relationship with 
        // the party then we are checking if
        // the logged user has a permission
        // to perform the action.
        if(!in_array($loggedUserPartyRelationship->roleId, $this->partyUserRoles->userModifiers())) {
            \App::abort(403, "You don't have enough priviliges to perform this action.");
        }

        // now we are checking if the requested
        // has a relationship with the party.
        // if not we are simply returning false,
        // to notify that there is not a relationship
        // between the user and the party
        $partyUser = static::where('partyId', '='. $partyId)->where('userId', '=', $userId)->first();
        if(!$partyUser) return false;

        $partyUser->roleId = $this->partyUserRoles->actionToId($action);
        $partyUser->save();

        return true;
    }

    /**
     * Edit the status of a user in a party,
     * if necessary create new.
     * @param  integer $partyId
     * @param  integer $userId
     * @param  integer $statusId
     * @return mixed boolean, $this
     */
    public function editStatus($partyId, $userId, $statusId)
    {
        // checking if there is any record about
        // containing both user and party.
        $partyUser = static::where('partyId', '=', $partyId)->where('userId', '=', $userId)->first();

        if($partyUser) {
            // if, the necessary statusId is different 
            // than the the partyUser's status id,
            // save the necessary statusId, if not
            // just return true.
            if($partyUser->statusId !== $statusId) {
                $partyUser->statusId = $statusId;
                $partyUser->save();
            }
            return true;
        }

        // create the necessary info to create a
        // partyUser record.
        $partyUserArray = [
            'partyId' => (int)$partyId, 
            'userId' => $userId, 
            'roleId' => $this->partyUserRoles->memberId(),
            'statusId' => $statusId
        ];

        $partyUser = new PartyUser($partyUserArray);


        if($partyUser->save()) {
            return true;
        }
        return $partyUser;
    }

    public function joinParty($partyId)
    {
        return $this->editStatus($partyId, \Auth::user()->id, static::$statuses['joined']);
    }

    public function unjoinParty($partyId)
    {
        return $this->editStatus($partyId, \Auth::user()->id, static::$statuses['unjoined']);
    }

    public function checkin($partyId)
    {
        return $this->editStatus($partyId, \Auth::user()->id, static::$statuses['checkin']);
    }

    public function checkout($partyId)
    {
        return $this->editStatus($partyId, \Auth::user()->id, static::$statuses['checkout']);
    }

    public function joining($partyId)
    {
        return static::with('user')->where('partyId', '=', $partyId)->where('statusId', '=', static::$statuses['joined'])->get();
    }

    public function checkins($partyId)
    {
        return static::with('user')->where('partyId', '=', $partyId)->where('statusId', '=', static::$statuses['checkin'])->get();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }

}