<?php
// import facade

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;



// RANDOM NUMBER GENERATOR FOR ELSEWHERE
if (!function_exists('random')) {
    function random($length_of_string, $lowercase = false)
    {
        // String of all alphanumeric character
        $str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

        // Shufle the $str_result and returns substring
        // of specified length
        $randVal = substr(str_shuffle($str_result), 0, $length_of_string);
        if ($lowercase) {
            $randVal = strtolower($randVal);
        }
        return $randVal;
    }
}

if (!function_exists('get_settings')) {
    function get_settings($type = "", $return_type = "")
    {
        $value = DB::table('settings')->where('type', $type)->value('description');
        if ($return_type === true) {
            return json_decode($value, true);
        } elseif ($return_type === 'decode') {
            return json_decode($value, true);
        } elseif ($return_type == "object") {
            return json_decode($value);
        } else {
            return $value;
        }
    }
}


//get system dark logo
if (!function_exists('get_system_logo_favicon')) {
    function get_system_logo_favicon($file_name = "", $foldername = "")
    {
        //this file comes from another online link as like amazon s3 server
        if (strpos($file_name, 'https://') !== false) {
            return $file_name;
        }

        $foldername = $foldername . '/';

        if (!empty($file_name)) {
            if (File::exists('public/storage/logo/' . $foldername . $file_name)) {
                return asset('storage/logo/' . $foldername . $file_name);
            } else {
                return asset('storage/logo/' . $foldername . 'default/default.jpg');
            }
        } else {
            return asset('storage/logo/' . $foldername . 'default/default.jpg');
        }
    }
}