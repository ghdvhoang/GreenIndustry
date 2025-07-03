<?php

namespace App\Http\Controllers;

use App\Models\FileUploader;
use App\Models\Posts;
use App\Models\Report;
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
    

    public function reported_post_to_admin()
    {
        $page_data['reported_post'] = Report::orderBy('id', 'DESC')->where('status', '0')->get();
        $page_data['view_path'] = 'reported_post.report';
        return view('backend.index', $page_data);
    }

    public function reported_post_remove_by_admin($id)
    {
        $done = Posts::where('post_id', $id)->update(['report_status' => '1']);
        Report::where('post_id', $id)->update(['status' => '1']);
        flash()->addSuccess('This Reported Item Delete Successfully');

        return redirect()->back();
    }
}
