<?php namespace Codeaqua\Models;

class GroupRole extends BaseModel {

    protected $table = 'groupRoles';

    protected $guarded = [];

    public static $rules = [
        'name'                 => 'required|numeric',
        'canEditPage'          => 'required|numeric',
        'canClosePage'         => 'required|numeric',
        'canCreateParty'       => 'required|numeric',
        'canDeleteParty'       => 'required|numeric',
        'canDeleteWallPost'    => 'required|numeric',
        'canDeleteWallComment' => 'required|numeric',
        'canSeeRequest'        => 'required|numeric',
        'canInvitePeople'      => 'required|numeric',
        'canEditMember'        => 'required|numeric',
        'canDeleteMember'      => 'required|numeric'
    ];
}