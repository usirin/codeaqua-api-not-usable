<?php

use Codeaqua\Response as CAQResponse;

use Codeaqua\Models\User;
use Codeaqua\Models\Friendship;
use Codeaqua\Models\Photo;
use Codeaqua\Models\Location;
use Codeaqua\Models\Group;
use Codeaqua\Models\Party;

use Codeaqua\Controllers\UsersController;
use Codeaqua\Controllers\FriendsController;
use Codeaqua\Controllers\PhotosController;
use Codeaqua\Controllers\LocationsController;
use Codeaqua\Controllers\GroupsController;
use Codeaqua\Controllers\GroupsUsersController;
use Codeaqua\Controllers\PartiesController;
use Codeaqua\Controllers\PartiesUsersController;
use Codeaqua\Controllers\PartiesVotesController;

Route::get('/', function()
{
    echo "http://" . $_SERVER['HTTP_HOST'] . ' ~~~~~' . $_SERVER['REQUEST_URI'];
});

Route::post('v0', function()
{

});

Route::get('asd', ['before' => 'api-auth', function(){
    var_dump(\Auth::user()->id);
    var_dump(\Auth::user()->id);
    var_dump(\Auth::user()->id);
}]);

Route::get('user/{name}', function($name)
{
    return 'string';
})
->where('name', '[A-Za-z]+');

Route::get('user/{id}', function($id)
{
    return 'integer';
})
->where('id', '[0-9]+');

Route::group(array('prefix' => 'v0'), function()
{

    Route::post('login', ['before' => 'api-login', function(){
        $user = Auth::user();
        
        $hiddenArray = [];
        foreach($user->getHidden() as $hidden) {
            if($hidden !== 'apiKey') {
                $hiddenArray[] = $hidden;
            }
        }

        $user->setHidden($hiddenArray);

        return CAQResponse::json($user);
    }]);


    /**
     * 
     */
    Route::post('signup', function() {
        $input = Input::all();
        $user = new User($input);
        
        // if it validates the rules, save it.
        if ($user->save()) {
            return CAQResponse::json($user, 201, 'User resource has been created successfully.');
        }

        return CAQResponse::error($user->getErrors());
    });

    Route::get('users/{id}/friends', FriendsController::class . '@getIndex');
    Route::post('users/{id}/friends', FriendsController::class . '@postIndex');
    Route::post('users/{userId}/friends/{friendId}', FriendsController::class . '@postUpdateFriendship');
    Route::get('users/{id}/requests', FriendsController::class . '@getFriendshipRequests');

    // definition of the routes of user related things.
    Route::resource('users', UsersController::class);

    Route::resource('photos', PhotosController::class);
    
    Route::get('locations/near', LocationsController::class . '@near');
    Route::resource('locations', LocationsController::class);

    Route::resource('groups', GroupsController::class);

    Route::post('groups/{id}/join', GroupsUsersController::class. '@join');
    Route::post('groups/{groupId}/users/{userId}', GroupsUsersController::class . '@editMembership');
    Route::get('groups/{groupId}/users', GroupsUsersController::class . '@users');

    

    Route::post('parties/{id}/join', PartiesUsersController::class . '@join');
    Route::post('parties/{id}/unjoin', PartiesUsersController::class . '@unjoin');
    Route::post('parties/{id}/checkin', PartiesUsersController::class . '@checkin');
    Route::post('parties/{id}/checkout', PartiesUsersController::class . '@checkout');
    Route::get('parties/{id}/checkins', PartiesUsersController::class . '@checkins');
    Route::get('parties/{id}/joining', PartiesUsersController::class . '@joining');
    Route::post('parties/{partyId}/users/{userId}', PartiesUsersController::class . '@edit');

    Route::post('parties/{id}/votes', PartiesVotesController::class . '@vote');
    Route::get('parties/{id}/votes', PartiesVotesController::class . '@index');
    Route::get('parties/{partyId}/users/{userId}/votes', PartiesVotesController::class . '@userVotes');

    Route::resource('parties', PartiesController::class);

});

