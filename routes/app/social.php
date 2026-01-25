<?php

//social-feeds
Route::get('/social-feeds', function(){
    return view('social.feeds');
})->name('social-feeds');
//social-profile
Route::get('/social-profile', function(){
    return view('social.profile');
})->name('social-feeds');
