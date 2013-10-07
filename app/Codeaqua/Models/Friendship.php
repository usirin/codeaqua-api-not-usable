<?php namespace Codeaqua\Models;

use Codeaqua\Models\BaseModel;
use Codeaqua\Models\User;

class Friendship extends BaseModel {

    protected $table = 'friendships';

    protected $guarded = [];
    
    public static $rules = [
        'sourceId' => 'required|numeric',
        'targetId' => 'required|numeric'
    ];

    /**
     * This shows that relationship is in 
     * request process, and it awaits the
     * necessary action from target user.
     *
     * @var integer
     */
    const RELATIONSHIP_STATUS_REQUEST = 1;

    /**
     * This shows that relationship has
     * occured and the source user and the
     * target user are friends.
     *
     * @var integer
     */
    const RELATIONSHIP_STATUS_FRIEND  = 2;

    /**
     * This shows that there was a friend
     * request from source user to target user
     * but target user denied it.
     *
     * @var integer
     */
    const RELATIONSHIP_STATUS_DENIED  = 3;

    /**
     * Check to see if the 2 different users
     * are friends or not.
     * @return boolean
     */
    public function exists($sourceId, $targetId)
    {
        $friendship = static::where(function($query) use ($sourceId, $targetId)
        {
            $query->where(function($query) use ($sourceId, $targetId)
            {
                $query->where('sourceId', '=', $sourceId);
                $query->where('targetId', '=', $targetId);
            });

            $query->orWhere(function($query) use ($sourceId, $targetId)
            {
                $query->where('sourceId', '=', $targetId);
                $query->where('targetId', '=', $sourceId);
            });
        })->first();

        return $friendship ? $friendship : false;
    }

    public function validate($rules)
    {
        if(parent::validate($rules)) {
            $targetId = $this->targetId;
            $sourceId = $this->sourceId;

            // check to see if there is a pending record for that one.
            // This is just for the creating sequence. Because,
            // whenever we are accepting or denying a friendship request
            // we will use the old row from db for that.
            if(static::exists($sourceId, $targetId)) {
                $this->errors[] = 'There is already a pending request between this users.';
                return false;
            }
            return true;
        }
    }

    public function sourceUser()
    {
        return $this->belongsTo(User::class, 'sourceId');
    }

    public function targetUser()
    {
        return $this->belongsTo(User::class, 'targetId');
    }

}