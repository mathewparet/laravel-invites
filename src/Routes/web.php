<?php

Route::group(['namespace' => 'mathewparet\LaravelInvites\Http\Controllers', 'middleware'=>'web'], function() {
    Route::get(config('laravelinvites.routes.follow'), 'LaravelInvitesController@accept')->name('laravelinvites.routes.follow');
});