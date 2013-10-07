<?php namespace Codeaqua\Controllers;

use Codeaqua\Response as CAQResponse;
use Codeaqua\Models\User;

class UsersController extends BaseController {

    public function __construct(User $users) {
        $this->beforeFilter('api-auth');
        $this->users = $users;
    }

    public function index()
    {
        $users = $this->users->with('profilePhoto')->get();
        if(empty($users)) {
            return CAQResponse::error(['Requested resource cannot be found'], 404);
        }
        return CAQResponse::json($users);
    }

    public function show($id)
    {
        if(is_numeric($id)) {
            $user = $this->users->find($id);
        }
        else if(is_string($id) && $id === 'self') {
            $user = \Auth::user();
        }
        else if(is_string($id)) {
            $user = $this->users->where('username', '=', $id)->first();
        }
        
        if(!$user) {
            return CAQResponse::error(['Requested user cannot be found'], 404);
        }

        $user->load('profilePhoto');

        return CAQResponse::json($user);
    }

    public function store()
    {
        // get the input. create the user object.
        $input = Input::all();
        $user = new User($input);
        
        // if it validates the rules, save it.
        if ($user->save()) {
            return CAQResponse::json($user, 201, 'User resource has been created successfully.');
        }

        return CAQResponse::error($user->getErrors());
    }


}