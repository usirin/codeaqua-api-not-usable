<?php namespace Codeaqua\Models;

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class User extends BaseModel implements UserInterface, RemindableInterface {

    protected $table = 'users';
    
    protected $hidden = ['password', 'apiKey', 'deletedAt'];

    protected $guarded = [];

    protected $with = ['profilePhoto'];

    public static $rules = [
        'username'  => 'required|unique:users',
        'password'  => 'required',
        'email'     => 'required|email|unique:users',
        'firstName' => 'required',
        'lastName'  => 'required',
        'birthdate' => 'required'
    ];

    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = \Hash::make($password);
        $this->attributes['apiKey'] = \Hash::make(\Str::random(8) . $password . \Str::random(8));
    }

    public function setBirthdateAttribute($birthdate)
    {
        $this->attributes['birthdate'] = new \DateTime($birthdate);
    }

    /**
     * Generating apiKey for the user whether
     * it is being created or, updated, it needed
     * to be in its own method. So this is that method.
     * @return Codeaqua\Model\User for chaining purposes.
     */
    public function generateApiKey()
    {
        $this->attributes['apiKey'] = \Hash::make(\Str::random(8) . $this->password . \Str::random(8));
        return $this;
    }
    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->password;
    }

    /**
     * Get the e-mail address where password reminders are sent.
     *
     * @return string
     */
    public function getReminderEmail()
    {
        return $this->email;
    }

    ///////////////////////
    //// Relationships ////
    ///////////////////////
    
    public function frienships()
    {
        return Friendship::where('sourceId', '=', $this->id)->orWhere('targetId', '=', $this->id)->get();
    }

    public function friends()
    {
        $self = $this;
        // get the friendships first.

        $friendships = Friendship::where(function($query) use ($self){
            $query->where('sourceId', '=', $self->id);
            $query->orWhere('targetId', '=', $self->id);
        })->where('status', '=', Friendship::RELATIONSHIP_STATUS_FRIEND)->get();
        // $friendships = Friendship::where('sourceId', '=', $this->id)
        //                     ->orWhere('targetId', '=', $this->id)
        //                     ->get();

        // this list will be filled up with the ids of
        // friends excluding logged user.
        $friendIdList = [];

        if(count($friendships) === 0) {
            return null;
        }
        // in this loop we are looking if the source id
        // is the user itself, or not, and we are
        // assigning the opposite id to the friendList.
        foreach ($friendships as $friendship) {
            $friendIdList[] = ($friendship->sourceId === $this->id) ? $friendship->targetId : $friendship->sourceId;
        }


        // get the necessary users from database as a collection
        // of models. So that it could still be available to
        // chaining.
        $friends = User::whereIn('id', $friendIdList)->get();

        return $this;
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'group_user', 'userId', 'groupId')->withPivot('groupRoleId');
    }

    public function profilePhoto()
    {
        return $this->belongsTo(Photo::class, 'photoId');
    }

    public function hostedParties()
    {
        return $this->morphMany(Party::class, 'hoster', 'hosterType', 'hosterId');
    }
}