<?php namespace Codeaqua;

use Illuminate\Support\Facades\Response as BaseResponse;
use Illuminate\Support\Contracts\ArrayableInterface;
use Codeaqua\Models\User;
use IteratorAggregate;

class Response {

    public static function json($data = array(), $status = 200, $message = null, array $headers = array())
    {

        // this means the object passed, not an array.
        // and object can be either a model,
        // or `Collection` of a model.
        if ($data instanceof ArrayableInterface) {

            // this means it is a collection
            if($data instanceof IteratorAggregate) {
                if(isset($data['resourceType'])) {
                    $resourceType = $data['resourceType'];
                    unset($data['resourceType']);
                }
                else {
                    $resourceType = get_class($data[0]);
                }
                $data = $data->toArray();
            }
            // this means it is a simple model
            else {
                if(isset($data['resourceType'])) {
                    $resourceType = $data['resourceType'];
                    unset($data['resourceType']);
                }
                else {
                    $resourceType = get_class($data);
                }
                $data = [$data->toArray()];
            }
        }
        else {
            if(isset($data['resourceType'])) {
                $resourceType = $data['resourceType'];
                unset($data['resourceType']);
            }
            else {
                $resourceType = get_class($data[0]);
            }
        }


        // this turns Codeaqua\Models\User into users, Codeaqua\Models\Party into parties etc.
        $resourceType = str_plural(strtolower(class_basename($resourceType)));

        // Create the final array to be passed into global \Response class.
        $returnArray = [
            'meta' => [
                'status' => $status,
            ],
            'notifications' => [
                // burasi guncellenecek
            ],
            'response' => [
                'count' => count($data),
                $resourceType => $data
            ]
        ];

        // If there is a message for the response
        // we will add that into the meta part of the
        // response.
        if ($message) {
            $returnArray['meta']['message'] = $message;
        }

        // Finally pass the generated array, status and the headers.
        return BaseResponse::json($returnArray, $status, $headers);
    }

    public static function message($messages = array(), $status = 200, array $headers = array())
    {
        $returnArray = [
            'meta' => ['status' => $status, 'messages' => $messages],
            'notifications' => []
        ];

        return BaseResponse::json($returnArray, $status, $headers);
    }

    public static function error($errors = array(), $status = 400, array $headers = array())
    {
        $returnArray = [
            'meta' => ['status' => $status],
            'notifications' => [],
            'response' => ['count' => count($errors), 'errors' => $errors]
        ];

        return BaseResponse::json($returnArray, $status, $headers);
    }
}
