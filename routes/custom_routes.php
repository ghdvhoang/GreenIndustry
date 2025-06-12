<?php

use App\Http\Controllers\CustomUserController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\Report\SearchController;
use Illuminate\Support\Facades\Route;

Route::controller(SearchController::class)->middleware('auth', 'verified',
//  'activity',
//   'prevent-back-history'
  )->group(function () {
    Route::get('/search', 'search')->name('search');
    Route::get('/search/people/', 'search_people')->name('search.people');
    Route::get('/search/post/', 'search_post')->name('search.post');
    Route::get('/search/video/', 'search_video')->name('search.video');
    Route::get('/search/product/', 'search_product')->name('search.product');
    Route::get('/search/page/', 'search_page')->name('search.page');
    Route::get('/search/group/', 'search_group')->name('search.group.specific');
    Route::get('/search/event/', 'search_event')->name('search.event');
});

Route::controller(CustomUserController::class)->middleware('auth',
 'verified',
//   'activity',
//    'prevent-back-history'
   )->group(function () {
    Route::get('user/view/profile/{id}', 'view_profile_data')->name('user.profile.view');
    Route::get('/user/load_post_by_scrolling', 'load_post_by_scrolling')->name('user.load_post_by_scrolling');
    Route::get('user/password/change', 'changepass')->name('user.password.change');
    Route::POST('user/password/update', 'updatepass')->name('user.password.update');
    Route::get('user/friend/{id}', 'friend')->name('user.friend');
    Route::get('user/unfriend/{id}', 'unfriend')->name('user.unfriend');

    Route::get('/user/friends/{id}', 'friends')->name('user.friends');
    Route::get('/user/photos/{id}/{identifire}', 'photos')->name('user.photos');
    Route::get('/user/videos/{id}', 'videos')->name('user.videos');

    Route::get('video/delete/{id}', 'delete_mediafile')->name('delete.mediafile');
    Route::get('download/media/file/{id}', 'download_mediafile')->name('download.mediafile');
    Route::get('download/media/file/image/{id}', 'download_mediafile_image')->name('download.mediafile.image');
});

Route::controller(PaidContent::class)->middleware('auth', 'verified',
//  'activity', 'prevent-back-history'
 )->group(function () {
    Route::get('/paid/content', 'paid_content')->name('paid.content');
    Route::get('/paid/content/general/timeline', 'general_timeline')->name('general.timeline');

    // payout and subscriber
    Route::get('/creator/payout', 'creator_payout')->name('creator.payout');
    Route::post('/creator/payout/request', 'creator_payout_request')->name('creator.payout.request');
    Route::get('/creator/payout/cancel/{id}', 'creator_payout_cancel')->name('creator.payout.cancel');
    Route::get('/paid/content/subscriber/', 'subscriber_list')->name('subscriber.list');

    Route::get('/user/subscription', 'user_subscription')->name('user.subscription');
    Route::get('/subscription/payment', 'subscription_payment')->name('subscription.payment');

    // creator profile view
    Route::get('/paid/content/view/{page}/{id}', 'creator_page_view')->name('page.view');
    Route::post('/paid/content/request/author/{id}', 'request_author')->name('request.author');

    // subscription
    Route::post('/paid/content/subscription/payment/{id}', 'subscription')->name('subscription.payment_configuration');

    // search type
    Route::post('/paid/content/search/{type}', 'search_type')->name('search.type');
    Route::get('/paid/content/search/{type}', 'search_type')->name('search.type');
    Route::get('/load/searched/list/item', 'load_search_list_item')->name('load.search.list.item');

    Route::get('/paid/content/creator/{type}', 'creator_timeline')->name('creator.timeline');
    Route::get('/creator/post/type/{type}', 'creator_timeline')->name('post.type');
    Route::get('/creator/subscribers/', 'subscribers')->name('creator.subscribers');
    Route::get('/creator/packages/', 'packages')->name('creator.package');
    Route::post('/paid/content/create/package', 'create_package')->name('create.package');
    Route::get('/paid/content/package/edit/{id}', 'edit_package')->name('edit.package');
    Route::post('/paid/content/package/update/{id}', 'update_package')->name('update.package');
    Route::get('/paid/content/package/delete/{id}', 'delete_package')->name('delete.package');

    Route::get('/paid/content/settings/', 'settings')->name('settings');
    Route::post('/paid/content/settings/update/{id}', 'update_settings')->name('update.settings');
    Route::get('/paid/content/settings/remove/{type}', 'remove_photo')->name('remove.photo');

    Route::post('/paid/content/my_page/post', 'post')->name('paid.content.post');
    Route::get('/load/paid/content/post/', 'load_paid_content_post')->name('load.paid.content.post');
    Route::get('/load/timeline/post/', 'load_timeline_post')->name('load.timeline.post');

    // admin
    Route::get('/admin/author/list', 'author_list')->name('author.list');
    Route::get('/admin/author/status/{id}', 'author_status')->name('author.status');
    Route::get('/admin/author/delete/{id}', 'author_delete')->name('author.delete');
    Route::get('/admin/author/review/request/{id}', 'review_request')->name('author.review.request');
    Route::get('/admin/author/payout/', 'payout_report')->name('payout.report');
    Route::get('/admin/author/pending/report', 'pending_report')->name('pending.report');
    Route::get('/admin/make/payment/{id}', 'author_payout')->name('author.payout');
    Route::get('/admin/payout/delete/{id}', 'delete_payout')->name('admin.delete.payout');
});

//  group
Route::controller(GroupController::class)->middleware('auth', 'verified',
//  'activity', 'prevent-back-history'
 )->group(function () {
    Route::get('/groups', 'groups')->name('groups');
    Route::POST('/group/store', 'store')->name('group.store');
    Route::post('/update/group/{id}', 'update')->name('group.update');
    Route::post('/update/coverphoto/group/{id}', 'updatecoverphoto')->name('group.coverphoto');
    Route::get('/group/peopel/info/{id}', 'peopelinfo')->name('group.people.info');
    Route::get('group/view/details/{id}', 'single_group')->name('single.group');
    Route::get('group/photo/view/{id}', 'group_photos')->name('single.group.photos');
    Route::get('all/peopel/group/view/{id}', 'all_people_group')->name('all.people.group.view');
    Route::get('/group/event/view/{id}', 'group_event')->name('group.event.view');
    Route::get('group/join/{id}', 'join')->name('group.join');
    Route::get('group/rjoin/{id}', 'rjoin')->name('group.rjoin');
    Route::get('group/search/view', 'search_group')->name('search.group');
    Route::get('group/all/view', 'group_all_view')->name('all.group.view');
    Route::get('group/user/create', 'group_user_create')->name('group.user.created');
    Route::get('group/user/joined', 'group_user_joined')->name('group.user.joined');
    Route::post('album/add/image', 'add_album_image')->name('add.image.album');
    Route::post('group/invites/sent', 'sent_invition')->name('group.invition');
    Route::get('/search_friends_for_inviting', 'search_friends_for_inviting')->name('search_friends_for_inviting');
    Route::get('/load_groups_by_scrolling', 'load_groups_by_scrolling')->name('load_groups_by_scrolling');

    // New Album List Page
    Route::get('album/details/list/{identifire}/{album_id}', 'album_details_list')->name('album.details.list');

    Route::get('album/details/page/list/{album_id}/{id}', 'album_details_page_list')->name('album.details.page.list');

    
    


});