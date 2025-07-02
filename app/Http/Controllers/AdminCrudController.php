<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\FileUploader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminCrudController extends Controller
{

    public function __construct()
    {

        //Don't remove it
        session(['admin_login' => 1]);
    }

    // admin change pass

    public function admin_change_password()
    {
        $page_data['view_path'] = 'profile_view.password';
        return view('backend.index', $page_data);
    }

    // admin profile

    public function admin_profile()
    {

        $page_data['view_path'] = 'profile_view.profile';
        return view('backend.index', $page_data);
    }

    public function admin_profile_update(Request $request)
    {
        $validated = $request->validate([
            'profile_photo' => 'mimes:jpeg,jpg,png,gif|nullable',
        ]);

        $user = auth()->user();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->date_of_birth = $request->dateofbirth;
        $user->profession = $request->profession;
        $user->gender = $request->gender;
        $user->phone = $request->phone;
        $user->address = $request->address;
        if ($request->profile_photo && !empty($request->profile_photo)) {

            $file_name = FileUploader::upload($request->profile_photo, 'public/storage/userimage', 800, null, 200, 200);
            //Update to database
            $user->photo = $file_name;
        }

        $user->save();
        flash()->addSuccess('Profile updated successfully!');
        return redirect()->back();
    }

    // dashboard

    public function admin_dashboard()
    {
        // $page_data['all_category'] = Pagecategory::all();
        $page_data['view_path'] = 'dashboard.index';
        return view('backend.index', $page_data);
    }

   

    public function about()
    {

        $purchase_code = get_settings('purchase_code');
        $returnable_array = array(
            'purchase_code_status' => get_phrase('Not found'),
            'support_expiry_date' => get_phrase('Not found'),
            'customer_name' => get_phrase('Not found'),
        );

        $personal_token = "gC0J1ZpY53kRpynNe4g2rWT5s4MW56Zg";
        $url = "https://api.envato.com/v3/market/author/sale?code=" . $purchase_code;
        $curl = curl_init($url);

        //setting the header for the rest of the api
        $bearer = 'bearer ' . $personal_token;
        $header = array();
        $header[] = 'Content-length: 0';
        $header[] = 'Content-type: application/json; charset=utf-8';
        $header[] = 'Authorization: ' . $bearer;

        $verify_url = 'https://api.envato.com/v1/market/private/user/verify-purchase:' . $purchase_code . '.json';
        $ch_verify = curl_init($verify_url . '?code=' . $purchase_code);

        curl_setopt($ch_verify, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch_verify, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch_verify, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch_verify, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch_verify, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');

        $cinit_verify_data = curl_exec($ch_verify);
        curl_close($ch_verify);

        $response = json_decode($cinit_verify_data, true);

        if (is_array($response) && isset($response['verify-purchase']) && count($response['verify-purchase']) > 0) {

            $item_name = $response['verify-purchase']['item_name'];
            $purchase_time = $response['verify-purchase']['created_at'];
            $customer = $response['verify-purchase']['buyer'];
            $licence_type = $response['verify-purchase']['licence'];
            $support_until = $response['verify-purchase']['supported_until'];
            $customer = $response['verify-purchase']['buyer'];

            $purchase_date = date("d M, Y", strtotime($purchase_time));

            $todays_timestamp = strtotime(date("d M, Y"));
            $support_expiry_timestamp = strtotime($support_until);

            $support_expiry_date = date("d M, Y", $support_expiry_timestamp);

            if ($todays_timestamp > $support_expiry_timestamp) {
                $support_status = 'expired';
            } else {
                $support_status = 'valid';
            }

            $returnable_array = array(
                'purchase_code_status' => $support_status,
                'support_expiry_date' => $support_expiry_date,
                'customer_name' => $customer,
                'product_license' => 'valid',
                'license_type' => $licence_type,
            );
        } else {
            $returnable_array = array(
                'purchase_code_status' => 'invalid',
                'support_expiry_date' => 'invalid',
                'customer_name' => 'invalid',
                'product_license' => 'invalid',
                'license_type' => 'invalid',
            );
        }

        $page_data['application_details'] = $returnable_array;
        $page_data['view_path'] = 'setting.system_about';
        return view('backend.index', $page_data);
    }

    public function curl_request($code = '')
    {

        $purchase_code = $code;

        $personal_token = "FkA9UyDiQT0YiKwYLK3ghyFNRVV9SeUn";
        $url = "https://api.envato.com/v3/market/author/sale?code=" . $purchase_code;
        $curl = curl_init($url);

        //setting the header for the rest of the api
        $bearer = 'bearer ' . $personal_token;
        $header = array();
        $header[] = 'Content-length: 0';
        $header[] = 'Content-type: application/json; charset=utf-8';
        $header[] = 'Authorization: ' . $bearer;

        $verify_url = 'https://api.envato.com/v1/market/private/user/verify-purchase:' . $purchase_code . '.json';
        $ch_verify = curl_init($verify_url . '?code=' . $purchase_code);

        curl_setopt($ch_verify, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch_verify, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch_verify, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch_verify, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch_verify, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');

        $cinit_verify_data = curl_exec($ch_verify);
        curl_close($ch_verify);

        $response = json_decode($cinit_verify_data, true);

        if (is_array($response) && count($response['verify-purchase']) > 0) {
            return true;
        } else {
            return false;
        }
    }

    //Don't remove this code for security reasons
    public function save_valid_purchase_code($action_type, Request $request)
    {

        if ($action_type == 'update') {
            $data['description'] = $request->purchase_code;

            $status = $this->curl_request($data['description']);
            if ($status) {
                DB::table('settings')->where('type', 'purchase_code')->update($data);
                session()->flash('message', get_phrase('Purchase code has been updated'));
                echo 1;
            } else {
                echo 0;
            }
        } else {
            return view('backend.admin.settings.save_purchase_code_form');
        }

    }

    

   

    public function blogs()
    {
        if (isset($_GET['delete']) && $_GET['delete'] == 'yes' && isset($_GET['id'])) {
            Blog::find($_GET['id'])->delete();
            flash()->addSuccess('Blog deleted successfully');
            return redirect()->back();
        }

        $page_data['view_path'] = 'blog.list';
        $page_data['blogs'] = Blog::get();
        return view('backend.index', $page_data);
    }

    public function blog_create()
    {
        $page_data['view_path'] = 'blog.create';
        return view('backend.index', $page_data);
    }

    public function blog_edit($id = "")
    {
        $page_data['blog_details'] = Blog::find($id)->first();
        $page_data['view_path'] = 'blog.edit';
        return view('backend.index', $page_data);
    }

    public function blog_created(Request $request)
    {

        if ($request->category == 'Select a category') {
            flash()->addError('Please select a category');
            return redirect()->back()->withInput();
        }

        $request->validate([
            'title' => 'required|max:255',
            'category' => 'required',
        ]);

        if ($request->image && !empty($request->image)) {
            $file_name = FileUploader::upload($request->image, 'public/storage/blog/thumbnail', 370);
            FileUploader::upload($request->image, 'public/storage/blog/coverphoto/' . $file_name, 900);
        }

        $data['user_id'] = Auth()->user()->id;
        $data['title'] = $request->title;
        $data['category_id'] = $request->category;
        $data['created_at'] = date('Y-m-d H:i:s', time());
        $data['updated_at'] = date('Y-m-d H:i:s', time());
        $tags = json_decode($request->tag, true);
        $tag_array = array();
        if (is_array($tags)) {
            foreach ($tags as $key => $tag) {
                $tag_array[$key] = $tag['value'];
            }
        }
        $data['tag'] = json_encode($tag_array);
        $data['description'] = $request->description;
        if ($request->image && !empty($request->image)) {
            $data['thumbnail'] = $file_name;
        }
        $data['view'] = json_encode(array());

        DB::Table('blogs')->insert($data);
        flash()->addSuccess('Blog created successfully');
        return redirect()->route('admin.blog');
    }

    public function blog_updated(Request $request, $id)
    {

        if ($request->category == 'Select a category') {
            flash()->addError('Please select a category');
            return redirect()->back()->withInput();
        }

        $request->validate([
            'title' => 'required|max:255',
            'category' => 'required',
        ]);

        if ($request->image && !empty($request->image)) {

            $file_name = FileUploader::upload($request->image, 'public/storage/blog/thumbnail', 370);
            FileUploader::upload($request->image, 'public/storage/blog/coverphoto/' . $file_name, 900);
        }

        $blog = Blog::find($id);

        // $blog->user_id = Auth()->user()->id;
        // store image name for delete file operation
        $imagename = $blog->thumbnail;

        $blog->user_id = Auth()->user()->id;
        $blog->title = $request->title;
        $blog->category_id = $request->category;
        $tags = json_decode($request->tag, true);
        $tag_array = array();

        if (is_array($tags)) {
            foreach ($tags as $key => $tag) {
                $tag_array[$key] = $tag['value'];
            }
        }
        $blog->tag = json_encode($tag_array);
        $blog->description = $request->description;
        !empty($request->image) ? $blog->thumbnail = $file_name : $blog->thumbnail;
        $done = $blog->save();
        if ($done) {
            // just put the file name and folder name nothing more :)
            if (!empty($request->image)) {
                removeFile('blog', $imagename);
            }
            flash()->addSuccess('Blog updated successfully');
            return redirect()->route('admin.blog');
        }
    }

}