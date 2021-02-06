<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'as' => 'api.', 'namespace' => 'API', 'middleware' => ['jwt-auth']], function () {

    /*User*/
    Route::post('logout', 'Auth\AuthController@logout');
    Route::post('refresh', 'Auth\AuthController@refresh');
    Route::resource('users', 'Auth\UserController');
    Route::post('auth/update/profile', 'Auth\UserController@authUpdateProfile');
    Route::post('auth/update/password', 'Auth\UserController@authUpdatePassword');
    Route::post('profile', 'Auth\UserController@profile');
    Route::post('update/user', 'Auth\UserController@updateuser');
    Route::post('status/user', 'Auth\UserController@userstatus');
    Route::post('verified/user', 'Auth\UserController@userverified');
    /*Proxy Manager*/
    Route::resource('proxy', 'ProxyManager\ProxyManagerController');
    Route::post('proxy/update', 'ProxyManager\ProxyManagerController@update');
    Route::post('proxy/statuschange', 'ProxyManager\ProxyManagerController@statuschange');
    /*Roles*/
    Route::post('roles/get/all', 'Role\RolesController@index')->name('roles.get.all');
    Route::post('roles/create', 'Role\RolesController@store')->name('roles.create');
    Route::post('roles/update', 'Role\RolesController@update')->name('roles.update');
    Route::post('roles/show', 'Role\RolesController@show')->name('roles.show');
    Route::post('roles/delete', 'Role\RolesController@destroy')->name('roles.delete');
    /*Permissions*/
    Route::post('permissions/get/all', 'Role\PermissionsController@index')->name('permissions.get.all');
    Route::post('permissions/create', 'Role\PermissionsController@store')->name('permissions.create');
    Route::post('permissions/update', 'Role\PermissionsController@update')->name('permissions.update');
    Route::post('permissions/show', 'Role\PermissionsController@show')->name('permissions.show');
    Route::post('permissions/delete', 'Role\PermissionsController@destroy')->name('permissions.delete');
    /*Settings Social Media*/
    Route::post('social/getAll','Settings\SocialNetworksController@list');
    Route::post('social/add','Settings\SocialNetworksController@add');
    Route::post('social/update','Settings\SocialNetworksController@update');
    Route::post('social/show','Settings\SocialNetworksController@show');
    Route::delete('social/delete','Settings\SocialNetworksController@delete');

    Route::post('social/apikey/set','Settings\SocialNetworksController@setkey');
    Route::post('social/apikey/get','Settings\SocialNetworksController@getkey');
    /*File Manager*/
    Route::post('upload/file', 'FileManager\UploadFile@create');
    Route::get('get/end/file', 'FileManager\UploadFile@userEndFile');
    Route::get('get/files', 'FileManager\UploadFile@userFiles');
    Route::post('get/file/history', 'FileManager\UploadFile@userFileHistory');
    Route::post('get/file/base', 'FileManager\UploadFile@getFileBase');
    Route::post('user/delete/image', 'FileManager\UploadFile@deleteUserFile');
    Route::post('update/image/user', 'FileManager\UploadFile@updateImageUser');

    /*Watermark*/
    Route::post('watermark/add', 'Post\WatermarkController@addWatermark')->name('watermark.add');
    Route::post('watermark/getAll', 'Post\WatermarkController@getWatermarks')->name('watermark.getAll');

    /*Notifications*/
    Route::get('get/notifications', 'Notifications\NotificationsController@getAuthNotifications');
    Route::post('send/notification', 'Notifications\NotificationsController@sendNotification');
    Route::get('read/notification', 'Notifications\NotificationsController@readAuthNotification');
    Route::get('get/notification/count', 'Notifications\NotificationsController@getNotificationCount');
    Route::post('end/notification', 'Notifications\NotificationsController@endAuthNotification');
    Route::post('create/notification', 'Notifications\NotificationsController@createNotification');
    /*Payments*/
    Route::post('create/payment', 'Payment\PaymentsController@create');
    Route::post('get/payment', 'Payment\PaymentsController@index');
    Route::post('all/get/payment', 'Payment\PaymentsController@allPayments');

    /*Ticket*/
    Route::post('ticket/new', 'Ticket\TicketController@newTicket')->name("ticket.new");
    Route::post('ticket/available/new', 'Ticket\TicketController@availableTicketAdd')->name("ticket.available.new");
    Route::get('ticket/get/categories', 'Ticket\TicketController@getTicketCategories')->name("ticket.get.categories");
    Route::post('ticket/get/message/list', 'Ticket\TicketController@getTicketMessageList')->name("ticket.get.message.list");
    Route::post('ticket/get/category/message/list', 'Ticket\TicketController@getCategoriesTicketList')->name("ticket.get.category.message.list");
    /*Roles*/
    Route::post('plan/get/all', 'Plan\PlanController@index')->name('plan.get.all');
    Route::post('plan/create', 'Plan\PlanController@store')->name('plan.create');
    Route::post('plan/update', 'Plan\PlanController@update')->name('plan.update');
    Route::post('plan/delete', 'Plan\PlanController@destroy')->name('plan.delete');
});



