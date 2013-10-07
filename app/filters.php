<?php

use Codeaqua\Models\User;
use Codeaqua\Response as CAQResponse;

App::before(function($request)
{
    if($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        $statusCode = 204;
        
        $headers = [
            'Access-Control-Allow-Origin'      => '*',
            'Allow'                            => 'GET, POST, OPTIONS',
            'Access-Control-Allow-Headers'     => 'Origin, Content-Type, Accept, Authorization, X-Requested-With',
            'Access-Control-Allow-Credentials' => 'true'
        ];

        return Response::make(null, $statusCode, $headers);
    }
});


App::after(function($request, $response)
{
    $response->headers->set('Access-Control-Allow-Origin', '*');
    $response->headers->set('Allow', 'GET, POST, OPTIONS');
    $response->headers->set('Access-Control-Allow-Headers', 'Origin, Content-Type, Accept, Authorization, X-Requested-With');
    $response->headers->set('Access-Control-Allow-Credentials', 'true');
    return $response;
});

Route::filter('auth', function()
{
	if (Auth::guest()) return Redirect::guest('login');
});


Route::filter('auth.basic', function()
{
	return Auth::basic();
});

Route::filter('api-login', function()
{
    // Test against the presence of Basic Auth credentials
    $creds = [
        'email' => Request::getUser(),
        'password' => Request::getPassword(),
    ];

    if ( ! Auth::attempt($creds) ) {
        return CAQResponse::error(['Unauthorized Request'], 401);
    }
});

Route::filter('api-auth', function(){
    if (!Request::getUser()) {
        App::abort(401, 'A valid API key is required');
    }

    $user = User::where('apiKey', '=', Request::getUser())->first();
    
    if (!$user) {
        App::abort(401, 'API key is not valid.');
    }

    Auth::login($user);
});






/*
|--------------------------------------------------------------------------
| Guest Filter
|--------------------------------------------------------------------------
|
| The "guest" filter is the counterpart of the authentication filters as
| it simply checks that the current user is not logged in. A redirect
| response will be issued if they are, which you may freely change.
|
*/

Route::filter('guest', function()
{
	if (Auth::check()) return Redirect::to('/');
});

/*
|--------------------------------------------------------------------------
| CSRF Protection Filter
|--------------------------------------------------------------------------
|
| The CSRF filter is responsible for protecting your application against
| cross-site request forgery attacks. If this special token in a user
| session does not match the one given in this request, we'll bail.
|
*/

Route::filter('csrf', function()
{
	if (Session::token() != Input::get('_token'))
	{
		throw new Illuminate\Session\TokenMismatchException;
	}
});

// Event::listen('illuminate.query', function($sql)
// {
//     var_dump($sql);
// }); 
