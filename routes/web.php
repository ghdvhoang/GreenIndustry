<?php

use App\Http\Controllers\MainController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;

Route::get('/clear-cache', function () {
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');

    return 'Application cache cleared';
});

Route::get('/auth-checker', function () {
    if (auth::check()) {
        return true;
    } else {
        return false;
    }
})->name('auth-checker');

//Passing param
Route::get('/users/{user_id}', function ($user_id) {
    return view('welcome');
});

Route::get('/email/verify/success', function () {
    return view('dashboard', ['verified' => true]);
})->name('verification.success');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::controller(MainController::class)->middleware('auth', 'verified', 'activity', 'prevent-back-history')->group(function () {
    Route::get('/', 'timeline')->name('timeline');
    Route::post('/create_post', 'create_post')->name('create_post');
    Route::get('/edit_post_form/{id}', 'edit_post_form')->name('edit_post_form');
    Route::post('/edit_post/{id}', 'edit_post')->name('edit_post');
    Route::get('/load_post_by_scrolling', 'load_post_by_scrolling')->name('load_post_by_scrolling');
    Route::post('/my_react', 'my_react')->name('my_react');
    Route::get('/my_comment_react', 'my_comment_react')->name('my_comment_react');
    Route::get('/post_comment', 'post_comment')->name('post_comment');
    Route::get('/load_post_comments', 'load_post_comments')->name('load_post_comments');
    Route::get('/search_friends_for_tagging', 'search_friends_for_tagging')->name('search_friends_for_tagging');

    Route::get('/live/{post_id}', 'live')->name('live');
    Route::get('/live-ended/{post_id}', 'live_ended')->name('zoom-meeting-leave-url');

    Route::get('/view/single/post/{id?}', 'single_post')->name('single.post');

    Route::get('/preview_post', 'preview_post')->name('preview_post');

    Route::get('/post_comment_count', 'post_comment_count')->name('post_comment_count');

    Route::post('/post/report/save/', 'save_post_report')->name('save.post.report');

    Route::get('/delete/my/post', 'post_delete')->name('post.delete');

    Route::get('comment/delete', 'comment_delete')->name('comment.delete');

    Route::post('share/on/group', 'share_group')->name('share.group.post');
    Route::post('share/on/my/timeline', 'share_my_timeline')->name('share.my.timeline');

    // share page view
    Route::get('custom/shared/post/view/{id}', 'custom_shared_post_view')->name('custom.shared.post.view');

    //remove media files
    Route::get('media/file/delete/{id}', 'delete_media_file')->name('media.file.delete');

    // main addon layout
    Route::get('addons/manager', 'addons_manager')->name('addons.manager');
    Route::get('/user/settings', 'user_settings')->name('user.settings');
    Route::post('/save/user/settings', 'save_user_settings')->name('save.payment.settings');

    // live streaming
    Route::get('/streaming/live/{id}', 'live_streaming')->name('go.live');



    // Theme Controller
    Route::post('/update-theme-color', 'updateThemeColor')->name('update-theme-color');


    Route::get('album/details/page_show/{id}', 'details_album')->name('album.details.page_show');


});
