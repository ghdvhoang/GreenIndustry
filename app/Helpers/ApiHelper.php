<?php
// import facade

use App\Models\Group_member;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;



if (!function_exists('get_user_info')) {
    function get_user_info($user_id = '')
    {
        $user_data = DB::table('users')->where('id', $user_id)->first();
        return $user_data;
    }
}
if (!function_exists('get_user_images')) {
    function get_user_images($file_name_or_user_id = "", $optimized = "")
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
                return url('public/storage/userimage/' . $optimized . $file_name);
            } else {
                return url('public/storage/userimage/default.png');
            }
        } elseif (File::exists('public/storage/userimage/' . $optimized . $file_name) && is_file('public/storage/userimage/' . $optimized . $file_name)) {
            return url('public/storage/userimage/' . $optimized . $file_name);
        } elseif (strpos($file_name, 'https://') !== false) {
            //this file comes from another online link as like amazon s3 server
            return $file_name;
        } else {
            return url('public/storage/userimage/default.png');
        }
    }
}
if (!function_exists('get_cover_photos')) {
    function get_cover_photos($file_name_or_user_id = '', $optimized = "")
    {

        $optimized = $optimized . '/';
        if ($file_name_or_user_id == '') {
            $file_name_or_user_id = Auth()->user()->cover_photo;
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
            $file_name = DB::table('users')->where('id', $user_id)->value('cover_photo');

            //this file comes from another online link as like amazon s3 server
            if (strpos($file_name, 'https://') !== false) {
                return $file_name;
            }

            if (File::exists('public/storage/cover_photo/' . $optimized . $file_name) && is_file('public/storage/cover_photo/' . $optimized . $file_name)) {
                return url('public/storage/cover_photo/' . $optimized . $file_name);
            } else {
                return url('public/storage/cover_photo/default.jpg');
            }
        } elseif (File::exists('public/storage/cover_photo/' . $optimized . $file_name) && is_file('public/storage/cover_photo/' . $optimized . $file_name)) {
            return url('public/storage/cover_photo/' . $optimized . $file_name);
        } elseif (strpos($file_name, 'https://') !== false) {
            //this file comes from another online link as like amazon s3 server
            return $file_name;
        } else {
            return url('public/storage/cover_photo/default.jpg');
        }
    }
}
if (!function_exists('get_post_images')) {
    function get_post_images($file_name = '', $optimized = "")
    {
        //this file comes from another online link as like amazon s3 server
        if (strpos($file_name, 'https://') !== false) {
            return $file_name;
        }

        $optimized = $optimized . '/';
        if (File::exists('public/storage/post/images/' . $optimized . $file_name) && is_file('public/storage/post/images/' . $optimized . $file_name)) {
            return url('public/storage/post/images/' . $optimized . $file_name);
        } else {
            return url('public/storage/post/images/default.png');
        }
    }
}
if (!function_exists('get_post_images')) {
    function get_post_images($file_name = '', $optimized = "")
    {
        //this file comes from another online link as like amazon s3 server
        if (strpos($file_name, 'https://') !== false) {
            return $file_name;
        }

        $optimized = $optimized . '/';
        if (File::exists('public/storage/post/images/' . $optimized . $file_name) && is_file('public/storage/post/images/' . $optimized . $file_name)) {
            return url('public/storage/post/images/' . $optimized . $file_name);
        } else {
            return url('public/storage/post/images/default.png');
        }
    }
}

if (!function_exists('get_post_videos')) {
    function get_post_videos($file_name = '', $optimized = "")
    {
        //this file comes from another online link as like amazon s3 server
        if (strpos($file_name, 'https://') !== false) {
            return $file_name;
        }

        if ($optimized != "") {
            $optimized = $optimized . '/';
        }
        if (File::exists('public/storage/post/videos/' . $optimized . $file_name)) {
            return url('public/storage/post/videos/' . $optimized . $file_name);
        } else {
            return url('public/storage/post/videos/default.png');
        }
    }
}

//get page logo
if (!function_exists('get_group_logos')) {
    function get_group_logos($file_name = "", $foldername = "")
    {
        //this file comes from another online link as like amazon s3 server
        if (strpos($file_name, 'https://') !== false) {
            return $file_name;
        }

        $foldername = $foldername . '/';

        // if (!empty($file_name) && !empty($foldername)) {
        if ($file_name != "" && $foldername != "") {
            return url('public/storage/groups/' . $foldername . $file_name);
        } else {
            return url('public/storage/groups/' . $foldername . 'default/default.jpg');
        }
        // }
        //  else {
        //     return url('public/storage/groups/' . $foldername . 'default/default.jpg');
        // }
    }

}