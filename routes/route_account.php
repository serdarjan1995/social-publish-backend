<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'namespace' => 'API', 'middleware' => ['jwt-auth']], function () {

    Route::post('account/get/all', 'AccountManager\AccountManagerController@getAll');
    Route::post('account/get/all/group', 'AccountManager\AccountManagerController@getAllGrouped');
    Route::post('account/change/status', 'AccountManager\AccountManagerController@changeStatus');
    Route::post('account/delete', 'AccountManager\AccountManagerController@delete');
    Route::post('account/add', 'AccountManager\AccountManagerController@add');
    Route::post('account/social/categories', 'AccountManager\AccountManagerController@getCategories');
    Route::post('account/add/get/link', 'AccountManager\AccountManagerController@getAddAccountLink');
    Route::post('account/add/code', 'AccountManager\AccountManagerController@getAccessTokenFromCode');
    #Twitter
    Route::post('account/add/twitterCode', 'AccountManager\AccountManagerController@getTwitterAccess');
    #Facebook
    Route::post('account/get/facebook/pages', 'AccountManager\FacebookAccountManagerController@getFacebookPages');
    Route::post('account/add/facebook/pages', 'AccountManager\FacebookAccountManagerController@addFacebookPages');
    Route::post('account/get/facebook/groups', 'AccountManager\FacebookAccountManagerController@getFacebookGroups');
    Route::post('account/add/facebook/groups', 'AccountManager\FacebookAccountManagerController@addFacebookGroups');
    #Linkedin
    Route::post('account/get/linkedin/company', 'AccountManager\LinkedinAccountManager@getLinkedinAdminCompanyPages');
    Route::post('account/add/linkedin/pages', 'AccountManager\LinkedinAccountManager@addLinkedinPages');

    #Group Manager
    Route::post('send/new/group', 'AccountManager\GroupManagerController@create');
    Route::get('get/all/group', 'AccountManager\GroupManagerController@getGroupName');
    Route::post('get/available/account', 'AccountManager\GroupManagerController@getAvailableAccount');
    Route::post('send/update/group', 'AccountManager\GroupManagerController@update');
    Route::post('send/delete/group', 'AccountManager\GroupManagerController@delete');
});

Route::group(['prefix' => 'v1', 'namespace' => 'SocialMediaApi', 'middleware' => ['jwt-auth']], function () {
    #Instagram
    Route::post('oauth/login/instagram', 'Instagram\Accounts@login')->name('instagram.oauth');
    Route::post('oauth/login/instagram/verify', 'Instagram\Accounts@login_2fa')->name('instagram.oauth.verify');
});
