<?php namespace Codeaqua\Models;

use Illuminate\Support\Contracts\ArrayableInterface;

use Eloquent;

class BaseModel extends Eloquent {
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    const DELETED_AT = 'deletedAt';

    public static $snakeAttributes = false;

    protected $hidden = ['deletedAt'];
    protected $softDelete = true;

    public static $rules;
    public static $updateRules = null;

    public $errors = null;

    public function getWithArray()
    {
        return $this->with;
    }

    public function getErrors()
    {
        if($this->errors instanceof ArrayableInterface)
        {
            $errorArray = [];
            foreach ($this->errors->toArray() as $key => $error) {
                $errorArray[] = $error[0];
            }

            return $errorArray;
        }
        return [$this->errors];
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function($model){
            return $model->validateForCreating();
        });

        static::updating(function($model) {
            return $model->validateForUpdating();
        });
    }

    /**
     * General validate method. Checks if the
     * necessary attributes set according to
     * the general rules defined in the static
     * property of $rules in this class.
     * 
     * @return Boolean
     */
    public function validate($rules)
    {
        $validation = \Validator::make($this->attributes, $rules);

        if($validation->passes()) {
            return true;
        }

        $this->errors = $validation->messages();
        return false;
    }

    /**
     * Validator for first creating a model.
     * To make different changes for creating,
     * or validating other than usual stuff 
     * (i.e controlling if there is a duplicate record)
     * this method should be overwritten.
     * 
     * @return Boolean
     */
    public function validateForCreating()
    {
        return $this->validate(static::$rules);
    }

    public function validateForUpdating()
    {
        $rules = static::$updateRules ? static::$updateRules : static::$rules;
        return $this->validate($rules);
    }
}