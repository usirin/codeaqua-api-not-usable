<?php namespace Codeaqua\Image;

use Codeaqua\Support\Helpers;
use Codeaqua\Image\ImageInterface;

class Image implements ImageInterface {

    protected $url;

    protected $imageData;

    const DEFAULT_UPLOAD_PATH = '/uploads/img';

    public function __construct($imageData)
    {
        $this->imageData = $imageData;
    }

    public function setImageData($imageData)
    {
        $this->imageData = $imageData;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function upload($path = null)
    {
        if($this->imageData === null) {
            \App::abort(400, 'You need to provide an image to upload');
        }

        if(null === $path) {
            $path = public_path() . static::DEFAULT_UPLOAD_PATH;
        }


        if($this->imageData->getError() !== 0) {
            \App::abort(409, 'There is something wrong with the image you provided.');
        }

        $fileInfo = Helpers::getFileNameAndExtension($this->imageData);

        $this->imageData->move($path, $fileInfo['fileName'] = \Str::random(20) . '.' . $fileInfo['extension']);
        $this->url = $fileInfo['fileName'];
        return true;
    }
}