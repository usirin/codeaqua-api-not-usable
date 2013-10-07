<?php namespace Codeaqua\Models;

class Party extends BaseModel {

    protected $table = 'parties';

    protected $guarded = [];

    protected $with = ['hoster', 'photo' ,'location', 'people'];

    public static $rules = [
        'hosterType'         => 'required',
        'hosterId'           => 'required',
        'typeId'             => 'required',
        'photoId'            => 'required',
        'name'               => 'required',
        'startTime'          => 'required',
        'endTime'            => 'required',
        'isPrivate'          => 'required',
        'isAlcohol'          => 'required',
        'isSmoking'          => 'required',
        'isDressCode'        => 'required',
        'isMusic'            => 'required',
        'isFood'             => 'required',
        'canAttendersInvite' => 'required'
    ];

    public static $types = [
        1 => 'House',
        2 => 'Outdoor'
    ];

    public function getTypeIdAttribute($value)
    {
        return static::$types[$value];
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['hosterType'] = explode('\\', $array['hosterType'])[2];

        if(isset($array['people'])) {
            for ($i = 0; $i < count($array['people']); $i++) { 
                $statusId = $array['people'][$i]['pivot']['statusId'];

                // This is a simple hack. I think
                // it is better to move it to a method,
                // but for now i will keep it like this
                // 7/26/2013
                $array['people'][$i]['partyStatus'] = array_keys(PartyUser::$statuses)[$statusId - 1];
                unset($array['people'][$i]['pivot']);
            }
        }
        return $array;
    }

    public static function boot()
    {
        parent::boot();

        static::created(function($party) {
            $partyUser = new PartyUser;
            $partyUser->partyId = $party->id;
            $partyUser->userId = \Auth::user()->id;
            $partyUser->roleId = $partyUser->partyUserRoles->owner();
            $partyUser->statusId = $partyUser::$statuses['joined'];
            $partyUser->save();
        });
    }

    public function hoster()
    {
        return $this->morphTo('hoster', 'hosterType', 'hosterId');
    }

    public function people()
    {
        return $this->belongsToMany(User::class, 'party_user', 'partyId', 'userId')->withPivot(['statusId']);
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'locationId');
    }

    public function photo()
    {
        return $this->belongsTo(Photo::class, 'photoId');
    }
}