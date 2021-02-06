<?php
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'namespace' => 'SocialMediaApi', 'middleware' => ['jwt-auth']], function () {
    #Instagram
    Route::post('instagram/analytics', 'Instagram\Analytics@get');
    #Linkedin
    Route::post('linkedin/page/statistics', 'Linkedin\Post@companyPageStatistics');
    Route::post('linkedin/page/statistics/details', 'Linkedin\Post@companyPageDetailStatistics');
    #Twitter
    Route::post('twitter/account', 'Twitter\Accounts@twitterAccountHandler')->name('twitter.account.handler');
    Route::post('twitter/analytics', 'Twitter\Analytics@twitterAnalyticsHandler')->name('twitter.analytic.handler');
});


Route::group(['prefix' => 'v1', 'namespace' => 'API\DirectMessage', 'middleware' => ['jwt-auth']], function () {
    #Instagram
    Route::post('direct/message/inbox', 'MessageController@inbox')->name('instagram.direct.message.inbox');
    Route::post('direct/message/show', 'MessageController@col_message')->name('instagram.direct.message.show');
    Route::post('direct/message/send', 'MessageController@send_message')->name('instagram.direct.message.send');
});
