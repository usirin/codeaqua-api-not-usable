<?php namespace Codeaqua\Controllers;

use Codeaqua\Models\User;
use Codeaqua\Models\Photo;
use Codeaqua\Image\Image;
use Codeaqua\Response as CAQResponse;

class PhotosController extends BaseController {

    public function __construct(User $users,
                                Photo $photos)
    {
        $this->beforeFilter('api-auth');

        $this->users = $users;
        $this->photos = $photos;
    }

    public function index()
    {
        $photos = $this->photos->with('user')->get();
        if(count($photos) === 0) {
            return CAQResponse::error(['Resource not found'], 404);
        }
        return CAQResponse::json($photos);
    }

    public function show($id)
    {
        $photo = $this->photos->with('user')->find($id);
        if(!$photo) {
            return CAQResponse::error(['Photo is not found'], 404);
        }
        return CAQResponse::json($photo);
    }

    public function store()
    {
        $rules = ['photo' => 'required|image'];
        $input = \Input::all();

        $validation = \Validator::make($input, $rules);

        $photo = new Photo();

        if ($validation->fails()) {
            $photo->errors = $validation->messages();
            return CAQResponse::error([$photo->getErrors()]);
        }

        $photo->setOriginalImage(new Image($input['photo']));
        $photo->userId = \Auth::user()->id;
        if (\Input::has('description')) {
            $photo->description = $input['description'];
        }

        if($photo->save()) {
            return CAQResponse::json($photo, 201, 'Image has been uploaded successfully');
        }
        return CAQResponse::error([$photo->getErrors()]);
    }
}