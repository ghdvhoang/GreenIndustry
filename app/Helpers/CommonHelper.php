<?php
// import facade

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;


if (!function_exists('addon_status')) {
    function addon_status($unique_identifier = '')
    {
        $result = DB::table('addons')->where('unique_identifier', $unique_identifier)->value('status');
        return $result;
    }
}

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

//All common helper functions
if (!function_exists('get_user_image')) {
    function get_user_image($file_name_or_user_id = "", $optimized = "")
    {

        $optimized = $optimized . '/';
        if ($file_name_or_user_id == '') {
            $file_name_or_user_id = 'default.png';
        }
        if (is_numeric($file_name_or_user_id)) {
            $user_id = $file_name_or_user_id;
            $file_name = "";
        } else {
            $user_id = "";
            $file_name = $file_name_or_user_id;
        }

        if ($user_id > 0) {
            $user_id = $file_name_or_user_id;
            $file_name = DB::table('users')->where('id', $user_id)->value('photo');

            //this file comes from another online link as like amazon s3 server
            if (strpos($file_name, 'https://') !== false) {
                return $file_name;
            }

            if (File::exists('public/storage/userimage/' . $optimized . $file_name) && is_file('public/storage/userimage/' . $optimized . $file_name)) {
                return asset('storage/userimage/' . $optimized . $file_name);
            } else {
                return asset('storage/userimage/default.png');
            }
        } elseif (File::exists('public/storage/userimage/' . $optimized . $file_name) && is_file('public/storage/userimage/' . $optimized . $file_name)) {
            return asset('storage/userimage/' . $optimized . $file_name);
        } elseif (strpos($file_name, 'https://') !== false) {
            //this file comes from another online link as like amazon s3 server
            return $file_name;
        } else {
            return asset('storage/userimage/default.png');
        }
    }
}
if (!function_exists('get_cover_photo')) {
    function get_cover_photo($file_name_or_user_id = '', $optimized = "")
    {

        $optimized = $optimized . '/';
        if ($file_name_or_user_id == '') {
            $file_name_or_user_id = Auth()->user()->photo;
        }
        if (is_numeric($file_name_or_user_id)) {
            $user_id = $file_name_or_user_id;
            $file_name = "";
        } else {
            $user_id = "";
            $file_name = $file_name_or_user_id;
        }

        if ($user_id > 0) {
            $user_id = $file_name_or_user_id;
            $file_name = DB::table('users')->where('id', $user_id)->value('photo');

            //this file comes from another online link as like amazon s3 server
            if (strpos($file_name, 'https://') !== false) {
                return $file_name;
            }

            if (File::exists('public/storage/cover_photo/' . $optimized . $file_name) && is_file('public/storage/cover_photo/' . $optimized . $file_name)) {
                return asset('storage/cover_photo/' . $optimized . $file_name);
            } else {
                return asset('storage/cover_photo/default.jpg');
            }
        } elseif (File::exists('public/storage/cover_photo/' . $optimized . $file_name) && is_file('public/storage/cover_photo/' . $optimized . $file_name)) {
            return asset('storage/cover_photo/' . $optimized . $file_name);
        } elseif (strpos($file_name, 'https://') !== false) {
            //this file comes from another online link as like amazon s3 server
            return $file_name;
        } else {
            return asset('storage/cover_photo/default.jpg');
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

if (!function_exists('script_checker')) {
    function script_checker($string = '', $convert_string = true)
    {

        if ($convert_string) {
            return nl2br(htmlspecialchars(strip_tags($string)));
        } else {
            return $string;
        }

    }
}

if (!function_exists('date_formatter')) {
    function date_formatter($strtotime = "", $format = "")
    {
        if ($strtotime && !is_numeric($strtotime)) {
            $strtotime = strtotime($strtotime);
        } elseif (!$strtotime) {
            $strtotime = time();
        }

        if ($format == "") {
            return date('d', $strtotime) . ' ' . date('M', $strtotime) . ' ' . date('Y', $strtotime);
        }

        if ($format == 1) {
            return date('D', $strtotime) . ', ' . date('d', $strtotime) . ' ' . date('M', $strtotime) . ' ' . date('Y', $strtotime);
        }

        if ($format == 2) {
            $time_difference = time() - $strtotime;
            if ($time_difference <= 10) {return get_phrase('Just now');}
            //864000 = 10 days
            if ($time_difference > 864000) {return date_formatter($strtotime, 3);}

            $condition = array(
                12 * 30 * 24 * 60 * 60 => get_phrase('year'),
                30 * 24 * 60 * 60 => get_phrase('month'),
                24 * 60 * 60 => get_phrase('day'),
                60 * 60 => 'hour',
                60 => 'minute',
                1 => 'second',
            );

            foreach ($condition as $secs => $str) {
                $d = $time_difference / $secs;
                if ($d >= 1) {
                    $t = round($d);
                    return $t . ' ' . $str . ($t > 1 ? 's' : '') . ' ' . get_phrase('ago');
                }
            }
        }

        if ($format == 3) {
            $date = date('d', $strtotime);
            $date .= ' ' . date('M', $strtotime);

            if (date('Y', $strtotime) != date('Y', time())) {
                $date .= date(' Y', $strtotime);
            }

            $date .= ' ' . get_phrase('at') . ' ';
            $date .= date('h:i a', $strtotime);
            return $date;
        }

        if ($format == 4) {
            return date('d', $strtotime) . ' ' . date('M', $strtotime) . ' ' . date('Y', $strtotime) . ', ' . date('h:i:s A', $strtotime);
        }
    }
}