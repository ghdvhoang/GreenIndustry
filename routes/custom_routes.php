<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\CustomUserController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Report\SearchController;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Route;




Route::controller(ChatController::class)->middleware('auth', 'verified')->group(function () {
    Route::get('/chat/inbox/{reciver}/{product?}/', 'chat')->name('chat');
    Route::POST('/chat/save', 'chat_save')->name('chat.save');
    Route::get('chat/own/remove/{id}', 'remove_chat')->name('remove.chat');
    Route::POST('/my_message_react', 'react_chat')->name('react.chat');
    Route::get('/chat/profile/search/', 'search_chat')->name('search.chat');

    Route::get('/chat/inbox/load/data/ajax/', 'chat_load')->name('chat.load');
    Route::get('/chat/inbox/read/message/ajax/', 'chat_read_option')->name('chat.read');
    
});
//  follow
Route::controller(FollowController::class)->middleware('auth', 'verified')->group(function () {
    Route::get('user/account/follow/{id}', 'follow')->name('user.follow');
    Route::get('user/account/unfollow/{id}', 'unfollow')->name('user.unfollow');
});

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

//  setting frontend
Route::controller(SettingController::class)->group(function () {
    Route::get('about/page/view/', 'about_view')->name('about.view')->middleware('auth', 'verified', 'prevent-back-history');
    Route::get('policy/page/view/', 'policy_view')->name('policy.view')->middleware('auth', 'verified', 'prevent-back-history');
    Route::get('contact/us/view/', 'contact_view')->name('contact.view');
    Route::POST('contact/us/send/', 'contact_send')->name('contact.send');

    Route::get('term/condition/view/', 'term_view')->name('term.view');

    Route::get('admin/about/page/data/', 'update_about_page_data')->name('admin.about.page.data.view')->middleware('auth', 'verified', 'admin', 'prevent-back-history');
    Route::POST('admin/about/page/data/update/{id}', 'update_about_page_data_update')->name('admin.about.page.data.update')->middleware('auth', 'verified', 'admin', 'prevent-back-history');

    Route::POST('admin/privacy/page/data/update/{id}', 'update_privacy_page_data_update')->name('admin.privacy.page.data.update')->middleware('auth', 'verified', 'admin', 'prevent-back-history');

    Route::POST('admin/term/page/data/update/{id}', 'update_term_page_data_update')->name('admin.term.page.data.update')->middleware('auth', 'verified', 'admin', 'prevent-back-history');

    Route::get('admin/reported/post/', 'reported_post_to_admin')->name('admin.reported.post.view')->middleware('auth', 'verified', 'admin', 'prevent-back-history');
    Route::get('admin/reported/post/delete/{id}', 'reported_post_remove_by_admin')->name('admin.reported.post.delete.by.admin')->middleware('auth', 'verified', 'admin', 'prevent-back-history');

    Route::get('admin/live-video/setting/view', 'live_video_edit_form')->name('admin.live-video.view')->middleware('auth', 'verified', 'admin', 'prevent-back-history');
    Route::post('admin/live-video/setting/update', 'live_video_update')->name('admin.live-video.update')->middleware('auth', 'verified', 'admin', 'prevent-back-history');

    Route::get('admin/smtp/setting/view/', 'smtp_settings_view')->name('admin.smtp.settings.view')->middleware('auth', 'verified', 'admin', 'prevent-back-history');
    Route::POST('admin/smtp/setting/save/{id}', 'smtp_settings_save')->name('admin.smtp.settings.view.save')->middleware('auth', 'verified', 'admin', 'prevent-back-history');

    // system settings
    Route::get('admin/system/setting/view/', 'system_settings_view')->name('admin.system.settings.view')->middleware('auth', 'verified', 'admin', 'prevent-back-history');
    Route::POST('admin/system/setting/save/', 'system_settings_save')->name('admin.system.settings.view.save')->middleware('auth', 'verified', 'admin', 'prevent-back-history');
    Route::POST('admin/system/setting/logo/save/', 'system_settings_logo_save')->name('admin.system.settings.logo.view.save')->middleware('auth', 'verified', 'admin', 'prevent-back-history');

    Route::get('admin/settings/amazon_s3', 'amazon_s3')->name('admin.settings.amazon_s3');
    Route::post('admin/settings/amazon_s3/update', 'amazon_s3_update')->name('admin.settings.amazon_s3.update');



    // Admin Color Save
    Route::get('admin/system/settings/color/save/{themeColor}', 'system_settings_color_save')->name('admin.system.settings.color.save')->middleware('auth', 'verified', 'admin', 'prevent-back-history');

    //Zitsi  Settings
     Route::get('admin/zitsi-video/setting/view', 'zitsi_video_edit_form')->name('admin.zitsi-video.view')->middleware('auth', 'verified', 'admin', 'prevent-back-history');
     Route::post('admin/jitsi/live/settings/update', 'zitsi_live_video_update')->name('admin.zitsi.live.settings.update');

});

Route::controller(NotificationController::class)->middleware('auth', 'verified')->group(function () {
    Route::get('/all/notification', 'notifications')->name('notifications');
    Route::get('/accept/friend/request/notification/{id}', 'accept_friend_notification')->name('accept.friend.request.from.notification');
    Route::get('/decline/friend/request/notification/{id}', 'decline_friend_notification')->name('decline.friend.request.from.notification');

    Route::get('/accept/group/request/notification/{id}/{group_id}', 'accept_group_notification')->name('accept.group.request.from.notification');
    Route::get('/decline/group/request/notification/{id}/{group_id}', 'decline_group_notification')->name('decline.group.request.from.notification');

    Route::get('/accept/event/request/notification/{id}/{event_id}', 'accept_event_notification')->name('accept.event.request.from.notification');
    Route::get('/decline/event/request/notification/{id}/{event_id}', 'decline_event_notification')->name('decline.event.request.from.notification');

    Route::get('/mark/as/read/notification/{id}', 'mark_as_read')->name('mark.as.read.notification');
    //fundraiser
    // Route::get('/accept/fundraiser/request/notification/{id}/{fundraiser_id}', 'accept_fundraiser_notification')->name('accept.fundraiser.request.from.notification');
    // Route::get('/decline/fundraiser/request/notification/{id}/{fundraiser_id}', 'decline_fundraiser_notification')->name('decline.fundraiser.request.from.notification');
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