<?php
// import facade

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

if (!function_exists('get_all_language')) {
    function get_all_language()
    {
        return DB::table('languages')->select('name')->distinct()->get();
    }
}

if (!function_exists('get_phrase')) {
    function get_phrase($phrase = '', $value_replace = array())
    {
        if (Session('active_language')) {
            $active_language = Session('active_language');
        } else {
            $active_language = get_settings('system_language');
            Session(['active_language' => get_settings('system_language')]);
        }
        $query = DB::table('languages')->where('name', $active_language)->where('phrase', $phrase);
        if ($query->count() > 0) {
            $tValue = $query->value('translated');
        } else {
            $tValue = $phrase;
            $all_language = get_all_language();

            if ($all_language->count() > 0) {
                foreach ($all_language as $language) {

                    if (DB::table('languages')->where('name', $language->name)->where('phrase', $phrase)->get()->count() == 0) {
                        DB::table('languages')->insert(array('name' => strtolower($language->name), 'phrase' => $phrase, 'translated' => $phrase));
                    }
                }
            } else {
                DB::table('languages')->insert(array('name' => 'english', 'phrase' => $phrase, 'translated' => $phrase));
            }
        }

        if (count($value_replace) > 0) {
            $translated_value_arr = explode('____', $tValue);
            $tValue = '';
            foreach ($translated_value_arr as $key => $value) {

                if (array_key_exists($key, $value_replace)) {
                    $tValue .= $value . $value_replace[$key];
                } else {
                    $tValue .= $value;
                }
            }
        }

        return $tValue;
    }
}

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