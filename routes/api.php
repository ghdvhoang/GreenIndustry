<?php

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/data', [ApiController::class,'userdata']);

Route::post('/login', [ApiController::class,'login']);
Route::post('/signup', [ApiController::class,'signup']);
Route::post('/forgot_password', [ApiController::class,'forgot_password']);
Route::post('/update_password', [ApiController::class,'update_password']);

Route::get('/timeline', [ApiController::class,'timeline']);
Route::get('/load_timeline', [ApiController::class,'load_timeline']);

Route::get('/friends', [ApiController::class,'friends']);
Route::post('/add_friend/{id}', [ApiController::class,'add_friend']);
Route::post('/unfriend/{id}', [ApiController::class,'unfriend']);
Route::post('/follow/{id}', [ApiController::class,'follow']);
Route::post('/unfollow/{id}', [ApiController::class,'unfollow']);
Route::get('/friend_request', [ApiController::class,'friend_request']);

Route::get('/getPostReactions/{postId}', [ApiController::class,'getPostReactions']);
Route::get('/timeline', [ApiController::class,'timeline']);
Route::get('/load_timeline', [ApiController::class,'load_timeline']);
Route::get('/stories', [ApiController::class,'stories']);
Route::post('/create_story', [ApiController::class,'create_story']);
Route::post('/reaction', [ApiController::class,'reaction']);
Route::post('/create_post', [ApiController::class,'create_post']);
Route::post('/edit_post/{id}', [ApiController::class,'edit_post']);
Route::post('/delete_post/{id}', [ApiController::class,'delete_post']);
Route::get('/post_media_file/{id}', [ApiController::class,'post_media_file']);
Route::post('/delete_media_file/{id}', [ApiController::class,'delete_media_file']);
Route::post('/save_post_report', [ApiController::class,'save_post_report']);

Route::get('/profile', [ApiController::class,'profile']);
Route::get('/other_profile/{id}', [ApiController::class,'other_profile']);
Route::post('/edit_profile', [ApiController::class,'edit_profile']);
Route::post('/update_profile_pic', [ApiController::class,'update_profile_pic']);
Route::post('/update_cover_pic', [ApiController::class,'update_cover_pic']);
Route::get('/profile_photos', [ApiController::class,'profile_photos']);
Route::get('/other_profile_photos/{id}', [ApiController::class,'other_profile_photos']);
Route::get('/single_post/{post_id}', [ApiController::class,'single_post']);
Route::get('/profile_videos', [ApiController::class,'profile_videos']);

Route::post('/post_comment', [ApiController::class,'post_comment']);
Route::post('/comment_reaction', [ApiController::class,'comment_reaction']);
Route::get('/get_comment/{postId}', [ApiController::class,'get_comment']);
Route::post('/comment_delete/{comment_id}', [ApiController::class,'comment_delete']);

Route::get('/groups', [ApiController::class,'groups']);
Route::get('/groups_details/{id}', [ApiController::class,'groups_details']);
Route::post('/create_group', [ApiController::class,'create_group']);
Route::post('/update_group/{group_id}', [ApiController::class,'update_group']);
Route::post('/updatecoverphoto_group/{group_id}', [ApiController::class,'updatecoverphoto_group']);
Route::post('/group_invition', [ApiController::class,'group_invition']);
Route::post('/groups_join/{id}', [ApiController::class,'groups_join']);
Route::post('/groups_join_remove/{id}', [ApiController::class,'groups_join_remove']);
Route::get('/groups_discussion/{group_id}', [ApiController::class,'groups_discussion']);
Route::get('/groups_people/{group_id}', [ApiController::class,'groups_people']);
Route::get('/groups_event/{group_id}', [ApiController::class,'groups_event']);
Route::get('/group_photos/{group_id}', [ApiController::class,'group_photos']);