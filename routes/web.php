<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/



Route::group (['middleware' => 'web'], function (){

    
    Route::get('/', 'TaskController@showUnclosedTasks');

    Route::post('/task', 'TaskController@createNewTask');
    Route::post('/task/start_{task}', 'TaskController@startTimer');
    Route::post('/task/stop_{task}', 'TaskController@stopTimer');
    Route::post('/task/get_stopwatch_time_{task}', 'TaskController@updateTimeOnPageReload');
    Route::post('/task/close_{task}', 'TaskController@closeTask');
    Route::post('/profile/restore_{task}', 'TaskController@restoreTask');

    Route::get('task/time_{task}', 'TaskController@updateFrontSpendTime');


    Route::delete('/profile/delete_{task}', 'TaskController@deleteTask');




    Route::get('/profile', 'ProfileController@showProfileTasks');

    Auth::routes();

    Route::get('/home', 'HomeController@index');

    Route::get('/register', function (){
        return view('auth/register');
    });
    Route::get('/login', function (){
        return view('/auth/login');
    });
    Route::get('/logout', function (){
        Auth::logout();

        return redirect('/');
    });







});


