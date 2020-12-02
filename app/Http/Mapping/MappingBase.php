<?php
namespace App\Http\Mapping;

class Mapping_Base
{
    /**
     * Remove all objects and array from an array/object
     * @param $object
     * @return array
     */
    protected function cleanArray($object)
    {

        $sorted = [];
        foreach ($object as $key => $value) {
            if (!is_array($value) && !is_object($value) && !is_null($value)) {
                $sorted[$key] = $value;
            }
        }
        return $sorted;
    }

    protected function cleanBulkArray($objects)
    {
        $sorted = [];
        $headers = [];
        foreach ($objects as $key1 => $obj) {
            foreach ($obj as $key2 => $value) {
                if (!is_array($value) && !is_object($value) && !is_null($value)) {
                    if (!in_array($key2, $headers)) {
                        $headers[] = $key2;
                    }

                    $sorted[$key1][$key2] = $value;
                }
            }
        }
        //now we add in the missing columns
        foreach ($sorted as $key => $obj) {
            foreach ($headers as $header) {
                if (!key_exists($header, $obj)) {
                    $sorted[$key][$header] = "{NULL}"; // insert as null into the DB
                }
            }
        }

        return $sorted;
    }
    
    /**
     * A helper function to check if a value isset properly if not return a default value
     * a cleaner way to do truthy  $value ? : 'default';
     * @param array|object $list
     * @param string|array $key
     * @param null $default
     * @return mixed
     */
    public function getValue($list, $key, $default = null)
    {
        if (!is_array($list) && !is_object($list)) { // if the first value is not an array return default
            return $default;
        }
        
        if (!is_array($key)) {
            $key = [$key];
        }
        
        $current = $list;
        foreach ($key as $element) {
            if (is_array($current)) {
                if (!isset($current[$element])) {
                    return $default;
                }
                $current = $current[$element];
            } else {
                if (!isset($current->$element)) {
                    return $default;
                }
                $current = $current->$element;
            }
        }
        
        return $current;
    }
}
