<?php namespace Codeaqua\Models;

use Codeaqua\Models\BaseModel;
use Codeaqua\Image\ImageInterface;

class Photo extends BaseModel {

    protected $table = 'photos';

    protected $guarded = [];

    public static $rules = [
        'photo'       => 'required',
        'description' => 'required',
        'userId'      => 'required|numeric'
    ];

    public static $updateRules = [
        'description' => 'required',
        'userId'      => 'required'
    ];

    /**
     * The original Image needs to be uploaded.
     * @var ImageInterface
     */
    protected $originalImage;

    public static function boot()
    {
        static::creating(function($model){
            return $model->uploadOriginal();
        });
        parent::boot();
    }

    public function setOriginalImage(ImageInterface $originalImage)
    {
        $this->originalImage = $originalImage;
    }

    public function uploadOriginal()
    {
        if($this->originalImage->upload()) {
        
            $this->attributes['original'] = $this->originalImage->getUrl();
            
            $queueArray = ['imageUrl' => $this->attributes['original']];

            \Queue::push('Codeaqua\Image\Resizer', $queueArray);
            return true;
        }
        return false;
    }

    #######################
    #### Relationships ####
    #######################

    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }

}