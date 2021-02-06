<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'as' => 'api.', 'namespace' => 'API', 'middleware' => ['jwt-auth']], function () {
    Route::post('post/create', 'Post\PostController@add')->name('post.create');
    Route::post('post/update', 'Post\PostController@update')->name('post.update');
    Route::post('post/delete', 'Post\PostController@delete')->name('post.delete');
    Route::post('post/show', 'Post\PostController@show')->name('post.show');
    Route::post('post/getAll', 'Post\PostController@getAll')->name('post.get.all');
    Route::post('post/get/link_info', 'Post\PostController@getLinkInfo')->name('post.get.link.info');

    Route::post('post/user/schedules', 'Post\PostController@calendar')->name('post.user.schedules');

    Route::post('text/notes', 'Post\TextTemplateController@index')->name('notes.list');
    Route::post('text/notes/create', 'Post\TextTemplateController@store')->name('notes.store');
    Route::post('text/notes/show', 'Post\TextTemplateController@show')->name('notes.show');
    Route::post('text/notes/update', 'Post\TextTemplateController@update')->name('notes.update');
    Route::post('text/notes/delete', 'Post\TextTemplateController@destroy')->name('notes.destroy');

});
Route::group(['prefix' => 'v1', 'namespace' => 'SocialMediaApi', 'middleware' => ['jwt-auth']], function () {
    #Linkedin
    Route::post('linkedin/page/post/delete', 'Linkedin\Post@companyPageCommentDelete');
    Route::post('linkedin/post/comments', 'Linkedin\Post@companyPostComments');
    Route::post('linkedin/post/comment/write', 'Linkedin\Post@companyPostCommentWrite');
    #Twitter
    Route::post('twitter/postaction', 'Twitter\Accounts@twitterPostActionsHandler')->name('twitter.postAction.handler');
});
