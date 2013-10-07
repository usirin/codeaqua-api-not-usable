<?php namespace Codeaqua\Models;

class PartyUserRole extends BaseModel {
    protected $table = 'partyUserRoles';

    protected $guarded = [];

    public static $rules = [
        'name'                => 'required',
        'canCloseParty'       => 'required',
        'canSendNotification' => 'required',
        'canEditPeople'       => 'required',
        'canDeletePeople'     => 'required',
        'canDeleteOthersPost' => 'required'
    ];

    public function userModifiers()
    {
        $idList = static::where('canEditPeople', '=', 1)->lists('id');
        return $idList;
    }

    public function memberId()
    {
        $object = static::where('name', '=', 'member')->first();
        return $object->id;
    }

    public function actionToId($action)
    {
        $action = ucfirst(explode('_', $action)[1]);
        $object = static::where('name', '=', $action)->first();
        return $object->id;
    }

    public function owner()
    {
        $object = static::where('name', '=', 'Owner')->first();
        return $object->id;
    }
}