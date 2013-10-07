<?php namespace Codeaqua\Models;


class Group extends BaseModel {
    protected $table = 'groups';

    protected $guarded = [];
    
    public static $rules = [
        'groupTypeId' => 'required|numeric',
        'name'        => 'required'
    ];

    protected $with = ['owner', 'members', 'photo', 'location'];

    /**
     * Override of the original
     * toArray method to fill the needs.
     * 
     * @return array
     */
    public function toArray()
    {
        $array = parent::toArray();
        $array['owner'] = $array['owner'][0];
        return $array;
    }

    public function owner()
    {
        return $this->belongsToMany(User::class, 'group_user', 'groupId', 'userId')
                    ->where('group_user.groupRoleId', '=', GroupUser::$roles['owner']);
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'group_user', 'groupId', 'userId');
    }

    public function photo()
    {
        return $this->belongsTo(Photo::class, 'photoId');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'locationId');
    }
}