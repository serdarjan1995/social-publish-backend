<?php
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1/auth', 'namespace' => 'API'], function () {

    Route::post('login', 'Auth\AuthController@login')->name('login');
    Route::post('register', 'Auth\RegistrationController@register')->name('register');

    Route::post('password/forgot', 'Auth\ForgotPasswordController@forgot')->name('password.forgot');
    Route::post('password/reset', 'Auth\ForgotPasswordController@reset')->name('password.reset');

    Route::get('email/verify/{id?}', 'Auth\VerificationController@verify')->name('verification.verify');
    Route::get('email/resend', 'Auth\VerificationController@resend')->name('verification.resend');

});

Route::group(['prefix' => 'v1', 'namespace' => 'API', 'middleware' => ['jwt-auth']], function () {

    Route::post('logout', 'Auth\AuthController@logout')->name('user.logout');
    Route::post('token/refresh', 'Auth\AuthController@refresh')->name('token.refresh');
    Route::post('rules/check', 'Role\RulesController@check')->name('rules.check');

});

