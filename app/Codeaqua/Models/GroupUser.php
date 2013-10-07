<?php namespace Codeaqua\Models;

class GroupUser extends BaseModel {

    protected $table = 'group_user';

    protected $guarded = [];

    public static $rules = [
        'groupId'     => 'required',
        'userId'      => 'required',
        'groupRoleId' => 'required'
    ];

    public static $roles = [
        'owner'   => 1,
        'officer' => 2,
        'member'  => 3
    ];

    public static function initFromDatabase($groupId, $userId) {
        // First, we are checking if the logged user has a membership
        // to the group.
        $loggedUserRelationship = static::where('groupId', '=', $groupId)->where('userId', '=', \Auth::user()->id)->first();

        // Then we are getting the groupRole ids that can edit members.
        $groupTypesCanEditMembers = GroupRole::where('canEditMember', '=', '1')->lists('id');

        if($loggedUserRelationship === null || in_array(\Auth::user()->id, $groupTypesCanEditMembers) === false) {
            return CAQResponse::json(null, 403, 'You don\'t have enough priviliges to perform this action');
        }

        $groupUser = static::where('groupId', '=', $groupId)->where('userId', '=', $userId)->first();
        if(!$groupUser) {
            return CAQResponse::error(['The user is not the member of this group'], 404);
        }
        return $groupUser;
    }

    public function promote()
    {
        if($this->groupRoleId === static::$roles['owner'] || $this->groupRoleId === static::$roles['officer']) {
            return false;
        }
        $this->groupRoleId = static::$roles['officer'];
        $this->save();
        return true;
    }

    public function demote()
    {
        if($this->groupRoleId === static::$roles['member']) {
            return false;
        }
        $this->groupRoleId = static::$roles['member'];
        $this->save();
        return false;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }

    public function group()
    {
        return $this->belongsTo(Group::class, 'groupId');
    }

    public function groupRole()
    {
        return $this->belongsTo(GroupRole::class, 'groupRoleId');
    }
}