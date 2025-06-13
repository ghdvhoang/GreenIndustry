<?php

namespace App\Http\Controllers;

use App\Models\FileUploader;
use App\Models\Group;
use App\Models\Group_member;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
class ApiController extends Controller
{
    public function login(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        // email checking
        $user = User::where('email', $fields['email'])->first();

        // Check password
        if (!$user || !Hash::check($fields['password'], $user->password)) {
            if (isset($user) && $user->count() > 0) {
                return response([
                    'message' => 'Invalid credentials!'
                ], 401);
            } else {
                return response([
                    'message' => 'User not found!'
                ], 401);
            }
        } else if ($user->user_role == 'general') {

            // $user->tokens()->delete();

            $token = $user->createToken('auth-token')->plainTextToken;

            // $user->photo = get_photo('user_image', $user->photo);

            $response = [
                'message' => 'Login successful',
                'user' => $user,
                'user_id' => $user->id,
                'user_image' => get_user_images($user->id),
                'cover_photo' => get_cover_photos($user->id),
                'token' => $token
            ];

            return response($response, 201);

        } else {

            //user not authorized
            return response()->json([
                'message' => 'User not found!',
            ], 400);
        }

    }

    public function signup(Request $request)
    {
        // return $request->all();
        $response = array();

        // $request->validate([
        //     'name' => ['required', 'string', 'max:255'],
        //     'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        //     'password' => ['required'],
        // ]);
        // $request->validate([
        //     'name' => ['required', 'string', 'max:255'],
        //     'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        //     'password' => ['required', 'confirmed', Rules\Password::defaults()],
        // ]);

        $rules = array(
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()]
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return json_encode(array('validationError' => $validator->getMessageBag()->toArray()));
        }
        // return $response;
        $user = User::create([
            'user_role' => 'general',
            'username' => rand(100000, 999999),
            'name' => $request->name,
            'email' => $request->email,
            'friends' => json_encode(array()),
            'followers' => json_encode(array()),
            'timezone' => $request->timezone,
            'password' => Hash::make($request->password),
            'status' => 0,
            'lastActive' => Carbon::now(),
            'created_at' => time()
        ]);
        if ($user) {
            $response['success'] = true;
            $response['message'] = 'user create successfully';
        }
        event(new Registered($user));

        return $response;
    }
    public function forgot_password(Request $request)
    {
        $response = array();

        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = Password::sendResetLink(
            $request->only('email')
        );
        if ($status) {
            $response['success'] = true;
            $response['message'] = 'Reset Password Link send successfully to your email';
        }

        // return $status == Password::RESET_LINK_SENT
        //             ? back()->with('status', __($status))
        //             : back()->withInput($request->only('email'))
        //                     ->withErrors(['email' => __($status)]);
        return $response;
    }
    public function create_group(Request $request)
    {
        $token = $request->bearerToken();
        $response = array();

        if (isset($token) && $token != '') {

            $user_id = auth('sanctum')->user()->id;
            $rules = array(
                'image' => 'mimes:jpeg,jpg,png,gif|nullable',
                'name' => 'required|max:255',
                'privacy' => 'required|max:255',
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return json_encode(array('validationError' => $validator->getMessageBag()->toArray()));
            }

            if ($request->image && !empty($request->image)) {
                $file_name = FileUploader::upload($request->image, 'public/storage/groups/logo', 300);
            }

            $group = new Group();
            $group->user_id = $user_id;
            $group->title = $request->name;
            $group->subtitle = $request->subtitle;
            $group->about = $request->about;
            $group->privacy = $request->privacy;
            $group->status = $request->status;
            if ($request->image && !empty($request->image)) {
                $group->logo = $file_name;
            }
            $done = $group->save();
            if ($done) {
                $group_member = new Group_member();
                $group_member->group_id = $group->id;
                $group_member->user_id = $user_id;
                $group_member->role = 'admin';
                $group_member->is_accepted = '1';
                $done = $group_member->save();
                if ($done) {
                    $response['success'] = true;
                    $response['message'] = 'group create successfully';
                } else {
                    $response['success'] = false;
                    $response['message'] = 'Failed to group page';
                }
            }
        } else {
            $response['success'] = false;
            $response['message'] = 'Unauthorized access';
        }
        return response()->json($response);

    }
}
