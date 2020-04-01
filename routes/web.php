<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/home/search', 'HomeController@search')->name('home');
Route::get('/video', 'VideoPageController@index');

Route::post('/ajax', 'AjaxController@ajax_call');



Route::resource('comment', 'CommentController')->middleware('auth');

Route::resource('myprofile', 'MyprofileController')->middleware('auth');

Route::get('socialauth/{provider}', 'Auth\SocialAuthController@redirectToProvider');
Route::get('socialauth/{provider}/callback', 'Auth\SocialAuthController@handleProviderCallback');

// Route qui permet de connaître la langue active
Route::get('locale', 'LanguageController@getLang')->name('getlang');

// Route qui permet de modifier la langue
Route::get('locale/{lang}', 'LanguageController@setLang')->name('setlang');

Route::get('/UserProfile/{id}', 'UserProfileController@show_user')->middleware('auth');
