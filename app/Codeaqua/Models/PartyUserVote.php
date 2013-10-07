<?php namespace Codeaqua\Models;

class PartyUserVote extends BaseModel {
    protected $table = 'partyUserVotes';
    protected $guarded = [];
    public static $rules = [
        'partyUserId' => 'required',
        'vote'        => 'required'
    ];

    public static $allowedVotes = ['down', 'up'];

    public function getVoteAttribute($vote)
    {
        return static::$allowedVotes[$vote];
    }

    public function vote($partyId)
    {
        // if the vote is not in the request
        // as a parameter, or, the vote is not
        // in the allovedVoteTypes, we will throw
        // an error in human readable context.
        if(isset($this->attributes['vote']) === false || in_array($this->attributes['vote'], static::$allowedVotes) === false) {
            $message = 'Please provide an applicable action. (allowed actions: ';
            for ($i=0; $i < count(static::$allowedVotes); $i++) { 
                if($i === count(static::$allowedVotes) - 1) {
                    $message .= 'vote = ' . static::$allowedVotes[$i] . ')';
                    break;
                }
                $message .= 'vote = ' . static::$allowedVotes[$i] . ', ';
            }
            $this->errors[] = $message;
            return false;
        }

        // get necessary Party User relation,
        // so that we can identify it. If there
        // isn't any record about it, simply we will
        // return a human readable message says that,
        // user needs to checkin first.
        $partyUser = PartyUser::where('partyId', '=', $partyId)->where('userId', '=', \Auth::user()->id)->first();
        if(!$partyUser) {
            $this->errors[] = 'You need to checkin to the party first to be able to vote.';
        }

        // Same as we did above. But the difference is,
        // there is a relationship, but it is not checkin
        // or checkout, so that we are sending a message.
        if($partyUser->statusId == PartyUser::$statuses['invited'] || 
           $partyUser->statusId == PartyUser::$statuses['joined'] ||
           $partyUser->statusId == PartyUser::$statuses['unjoined']) {
            $this->errors[] = 'You need to checkin to the party first to be able to vote.';
        }

        // if the error count is bigger than 0
        // we won't do the saving.
        if(count($this->errors) > 0 ) {
            return false;
        }

        $this->partyUserId = $partyUser->id;

        // since db, doesn't know about 'up' or 'down'
        // we are changing it to an integer
        // depending on the index number of the
        // array.
        $this->vote = array_search($this->vote, static::$allowedVotes);

        if($this->save()) {
            return true;
        }
        return false;
    }
}