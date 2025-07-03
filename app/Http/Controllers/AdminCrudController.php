<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Blogcategory;
use App\Models\FileUploader;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

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

            $file_name = FileUploader::upload($request->profile_photo, 'storage/userimage', 800, null, 200, 200);
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

    // blog crud
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
            $file_name = FileUploader::upload($request->image, 'storage/blog/thumbnail', 370);
            FileUploader::upload($request->image, 'storage/blog/coverphoto/' . $file_name, 900);
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

            $file_name = FileUploader::upload($request->image, 'storage/blog/thumbnail', 370);
            FileUploader::upload($request->image, 'storage/blog/coverphoto/' . $file_name, 900);
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



    public function users()
    {
        $users = User::where('user_role', '!=', 'admin')->get();

        $page_data['users'] = $users;
        $page_data['view_path'] = 'users.list';
        return view('backend.index', $page_data);
    }

    public function user_add()
    {
        $page_data['view_path'] = 'users.add';
        return view('backend.index', $page_data);
    }

    public function user_store(Request $request)
    {
        //password validation
        //  $request->validate([
        //     'current_password' => ['required', new MatchOldPassword],
        //     'new_password' => ['required'],
        //     'new_confirm_password' => ['same:new_password'],
        // ]);

        $this->validate($request, [
            'email' => ['required', 'email', Rule::unique('users')],
            'name' => 'required', 'max:255',
            'gender' => 'required',
            'date_of_birth' => 'required',
        ]);

        if ($request->photo && !empty($request->photo)) {
           // $file_name = FileUploader::upload($request->photo, 'public/storage/userimage', 800);
            $file_name = FileUploader::upload($request->file('photo'), 'public/storage/userimage', 800);
            //Update to database
            $data['photo'] = $file_name;
        }

        $data['user_role'] = 'general';
        $data['name'] = $request->name;
        $data['email'] = $request->email;
        $data['password'] = Hash::make($request->password);
        $data['phone'] = $request->phone;
        $data['address'] = $request->address;
        $data['date_of_birth'] = strtotime($request->date_of_birth);
        $data['about'] = $request->bio;
        $data['friends'] = '[]';
        $data['followers'] = '[]';
        $data['gender'] = $request->gender;
        $data['status'] = 1;
        $date['created_at'] = now();

        $user_insert = User::create($data);
        $user_insert->markEmailAsVerified();
        flash()->addSuccess('User added successfully');
        return redirect()->route('admin.users');
    }

    public function user_edit($id = "")
    {
        $page_data['user_data'] = User::find($id);
        $page_data['view_path'] = 'users.edit';
        return view('backend.index', $page_data);
    }

    public function user_update($id = "", Request $request)
    {
        //password validation
        //  $request->validate([
        //     'current_password' => ['required', new MatchOldPassword],
        //     'new_password' => ['required'],
        //     'new_confirm_password' => ['same:new_password'],
        // ]);

        $this->validate($request, [
            'email' => ['required', 'email', Rule::unique('users')->ignore($id)],
            'name' => 'required', 'max:255',
            'gender' => 'required',
            'date_of_birth' => 'required',
        ]);

        if ($request->photo && !empty($request->photo)) {
            $file_name = FileUploader::upload($request->photo, 'public/storage/userimage', 800);

            $previous_image = public_path() . '/storage/userimage/optimized/' . User::where('id', $id)->value('photo');
            $previous_image2 = public_path() . '/storage/userimage/' . User::where('id', $id)->value('photo');
            if (file_exists($previous_image) && is_file($previous_image)) {
                unlink($previous_image);
                unlink($previous_image2);
            }

            //Update to database
            $data['photo'] = $file_name;
        }

        $data['name'] = $request->name;
        $data['email'] = $request->email;
        $data['phone'] = $request->phone;
        $data['address'] = $request->address;
        $data['date_of_birth'] = strtotime($request->date_of_birth);
        $data['about'] = $request->bio;
        $data['gender'] = $request->gender;
        $date['updated_at'] = now();

        User::where('id', $id)->update($data);
        flash()->addSuccess('User updated successfully');
        return redirect()->route('admin.users');
    }

    public function user_delete($user_id = "")
    {
        User::find($user_id)->delete();
        flash()->addSuccess('User deleted successfully');
        return redirect()->route('admin.users');
    }

    public function user_status($user_id = "")
    {
        $query = User::where('id', $user_id);
        if ($query->value('status') == 1) {
            $query->update(['status' => 0]);
        } else {
            $query->update(['status' => 1]);
        }
        flash()->addSuccess('User deleted successfully');
        return redirect()->route('admin.users');
    }


    public function server_side_users_data(Request $request)
    {
        // echo $total_number_of_row = User::where('user_role', '!=', 'admin')->count();
        // $users = User::skip(12)->take(12)->select('name', 'id', 'email', 'photo', 'status')->where('user_role', '!=', 'admin')->orderBy('id', 'asc')->get();
        // print_r($users);
        // die;

        $data = array();
        //mentioned all with colum of database table that related with number of html table
        $columns = array('id', 'id', 'name', 'email', 'status', 'id');

        $limit = $request->length;
        $start = $request->start;

        $column_index = $columns[$request->order[0]['column']];

        $dir = $request->order[0]['dir'];
        $total_number_of_row = User::where('user_role', '!=', 'admin')->count();

        $filtered_number_of_row = $total_number_of_row;
        $search = $request->search['value'];

        if (empty($search)) {
            $users = User::skip($start)->take($limit)->select('name', 'id', 'email', 'photo', 'status', 'email_verified_at')->where('user_role', '!=', 'admin')->orderBy($column_index, $dir)->get();
        } else {
            $users = User::where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            })
                ->where('user_role', '!=', 'admin');
            $filtered_number_of_row = $users->count();
            $users = $users->skip($start)->take($limit)->orderBy($column_index, $dir)->get();
        }

        foreach ($users as $key => $user):

            //photo
            $photo = '<img src="' . User::get_user_image($user['photo']) . '" alt="" height="50" width="50" class="img-fluid rounded-circle img-thumbnail">';

            //user name
            if ($user['email_verified_at'] == null) {$status = '<small><br><span class="badge bg-danger">' . get_phrase('Unverified') . '</span></small>';} else { $status = '';}
            $name = $user['name'] . $status;

            //user email
            $email = $user['email'];

            //Status
            if ($user['status'] != 1) {
                $status = '<span class="badge bg-danger">' . get_phrase('Disabled') . '</span>';
            } else {
                $status = '<span class="badge bg-success">' . get_phrase('Active') . '</span>';
            }

            if ($user['status'] != 1) {
                $status_btn = '<a class="dropdown-item" onclick="return confirm(&#39;' . get_phrase('Are You Sure') . ' ?&#39;)" href="' . route('admin.user.status', $user['id']) . '">' . get_phrase('Active') . '</a>';
            } else {
                $status_btn = '<a class="dropdown-item" onclick="return confirm(&#39;' . get_phrase('Are You Sure') . ' ?&#39;)" href="' . route('admin.user.status', $user['id']) . '">' . get_phrase('Deactive') . '</a>';
            }

            $action = '<div class="adminTable-action me-auto">
		                        <button type="button" class="eBtn eBtn-black dropdown-toggle table-action-btn-2" data-bs-toggle="dropdown" aria-expanded="false">
		                          ' . get_phrase("Actions") . '
		                        </button>
		                        <ul class="dropdown-menu dropdown-menu-end eDropdown-menu-2 eDropdown-table-action">
		                          <li><a class="dropdown-item" href="' . route('admin.user.edit', $user['id']) . '">' . get_phrase('Edit') . '</a>
		                          </li>
		                          <li>' . $status_btn . '</li>
		                          <li>
		                            <a class="dropdown-item" onclick="return confirm(&#39;' . get_phrase('Are You Sure Want To Delete') . ' ?&#39;)" href="' . route('admin.user.delete', $user['id']) . '">' . get_phrase('Delete') . '</a>
		                          </li>
		                        </ul>
		                    </div>';

            $nestedData['key'] = ++$key;
            $nestedData['photo'] = $photo;
            $nestedData['name'] = $name;
            $nestedData['email'] = $email;
            $nestedData['status'] = $status;
            $nestedData['action'] = $action . '<script>$("a, i").tooltip();</script>';
            $data[] = $nestedData;
        endforeach;

        $json_data = array(
            "draw" => intval($request->draw),
            "recordsTotal" => intval($total_number_of_row),
            "recordsFiltered" => intval($filtered_number_of_row),
            "data" => $data,
        );
        echo json_encode($json_data);
    }

    // blog category
    public function view_blog_category()
    {
        $page_data['all_category'] = Blogcategory::all();
        $page_data['view_path'] = 'blog_category.index';
        return view('backend.index', $page_data);
    }

    public function create_blog_category()
    {
        $page_data['view_path'] = 'blog_category.create';
        return view('backend.index', $page_data);
    }

    public function save_blog_category(Request $request)
    {
        $validated = $request->validate([
            'blogcategory' => 'required|max:255|string|unique:blogcategories,name',
        ]);
        $blogcategories = new Blogcategory();
        $blogcategories->name = $request->blogcategory;
        $done = $blogcategories->save();
        if ($done) {
            flash()->addSuccess('Blog Category has been added successfully!');
        }
        return redirect()->back();
    }

    public function edit_blog_category($id)
    {
        $page_data['blogcategories'] = Blogcategory::find($id);
        $page_data['view_path'] = 'blog_category.edit';
        return view('backend.index', $page_data);
    }

    public function update_blog_category(Request $request, $id)
    {
        $validated = $request->validate([
            'blogcategory' => 'required|max:255|string|unique:blogcategories,name,' . $id,
        ]);
        $blogcategories = Blogcategory::find($id);
        $blogcategories->name = $request->blogcategory;
        $done = $blogcategories->save();
        if ($done) {
            flash()->addSuccess('Blog Category has been updated successfully!');
        }
        return redirect()->route('admin.view.blog.category');
    }

    public function delete_blog_category($id)
    {
        $blogcategories = Blogcategory::find($id);
        $blogcategories->delete();
        flash()->addSuccess('Blog Category Brand has been Deleted successfully!');
        return redirect()->back();
    }
}