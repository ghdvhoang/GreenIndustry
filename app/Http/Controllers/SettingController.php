<?php

namespace App\Http\Controllers;

use App\Models\FileUploader;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function system_settings_logo_save(Request $request)
    {
        if ($request->hasFile('dark_logo')) {

            $dark_file_ext = $request->dark_logo->extension();
            $dark_file_name = rand(0, 1000) . '.' . $dark_file_ext;
            $done = Setting::where('type', 'system_dark_logo')->update(['description' => $dark_file_name]);
            if ($done) {
                FileUploader::upload($request->dark_logo, 'public/storage/logo/dark/' . $dark_file_name);
            }
        }

        if ($request->hasFile('light_logo')) {
            $light_file_ext = $request->light_logo->extension();
            $light_file_name = rand(0, 1000) . '.' . $light_file_ext;
            $done = Setting::where('type', 'system_light_logo')->update(['description' => $light_file_name]);
            if ($done) {
                FileUploader::upload($request->light_logo, 'public/storage/logo/light/' . $light_file_name);
            }
        }

        if ($request->hasFile('favicon')) {
            $favicon_ext = $request->favicon->extension();
            $favicon_file_name = rand(0, 1000) . '.' . $favicon_ext;
            $done = Setting::where('type', 'system_fav_icon')->update(['description' => $favicon_file_name]);
            if ($done) {
                FileUploader::upload($request->favicon, 'public/storage/logo/favicon/' . $favicon_file_name);
            }
        }
        flash()->addSuccess('Logo Updated Successfully');
        return redirect()->back();
    }
}
