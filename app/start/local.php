<?php

 //
// General HttpException handler
App::error(function(Symfony\Component\HttpKernel\Exception\HttpException $e, $code)
{
    $headers = $e->getHeaders();

    switch ($code)
    {
        case 400:
            $default_message = 'Bad Request';
        break;

        case 401:
            $default_message = 'Invalid API key';
            $headers['WWW-Authenticate'] = 'Basic realm="REST API"';
        break;

        case 403:
            $default_message = 'Insufficient privileges to perform this action';
        break;

        case 404:
            $default_message = 'The requested resource was not found';
        break;

        default:
            $default_message = 'An error was encountered';
    }

    $error = ['error' => true, 'message' => $e->getMessage() ?: $default_message];

    return Response::json($error, $code, $headers);
});