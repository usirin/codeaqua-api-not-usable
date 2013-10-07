<?php namespace Codeaqua\Models;

class Location extends BaseModel {

    protected $guarded = [];

    protected $table = 'locations';

    public static $rules = [
        'name'          => 'required',
        'streetAddress' => 'required',
        'city'          => 'required',
        'state'         => 'required',
        'country'       => 'required',
        'postalCode'    => 'required',
        'longitude'     => 'required',
        'latitude'      => 'required'
    ];

    const ACCURACY_DEFAULT = 10;

    public static function near(array $input = array())
    {
        $rules = [
            'longitude' => 'required',
            'latitude'  => 'required'
        ];

        $validation = \Validator::make($input, $rules);

        if ($validation->fails()) {
            $this->errors = $validation->messages();
            return false;
        }

        $accuracy = isset($input['accuracy']) ? $input['accuracy'] : static::ACCURACY_DEFAULT;

    }

    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }
}