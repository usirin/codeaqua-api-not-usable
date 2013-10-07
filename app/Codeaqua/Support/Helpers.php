<?php namespace Codeaqua\Support;

class Helpers {

    /**
     * Returns an array consists of the name and
     * the extension of the file
     * @param  \File    $file 
     * @return array
     */
    public static function getFileNameAndExtension($file)
    {
        $array = [];

        $array['name'] = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $array['extension'] = strtolower(\File::extension($file->getClientOriginalName()));
        $array['fileName'] = $array['name'] . '.' . $array['extension'];

        return $array;
    }
}