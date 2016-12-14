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

use App\Task;

use Illuminate\Support\Facades\Db;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Foundation\Validation;


Route::group (['middleware' => 'web'], function (){

    /**
     * Main page
     */
    
    Route::get('/', function () {
        $tasks = Task::orderBy('created_at', 'desc')
            ->where('id_user', Auth::id())
            ->get();

        $active_task = 0;
        $time = 0;

        // set that 
        if( Task::where('id_user', Auth::id())->where('is_active', 1)->exists() )
        {
            $active_task = 1;

            $unclosed_task = Db::table('tasks')
                ->where('id_user', Auth::id())
                ->where('is_active', 1)
                ->pluck('time_started');
            $time = Task::calculateMissedTime($unclosed_task[0]);
        }


        return view('tasks', [
            'tasks' => $tasks,
            'active_task' => $active_task,
            'time' => $time,
        ]);
    });

    /**
     * Add New Task
     */

    Route::post('/task', function (Request $request)
    {
        $validator = Validator::make($request->all(), [
           'name' => 'required|max:255',
        ]);

         if ($validator->fails()) {
             return redirect('/')
                 ->withInput()
                 ->withErrors($validator);
         }

        $task = new Task;
        $task->id_user = Auth::id();
        $task->name = $request->name;
        $task->time_started = time();
        $task->save();

        return redirect('/');

    });

    /**
     * Start timer
     */
    
    Route::post('/task/start_{task}', function (Task $task) {
        $task->time_started = time();
        $task->is_active = 1; 
        $task->update();

       return redirect('/');
    });

    /**
     * Stop timer
     */

    Route::post('/task/stop_{task}', function (Task $task) {
        $task->session_duration =  abs( $task->time_started - time() );
        $task->total_duration += $task->session_duration;
        $task->is_active = 0;
        $task->update();

        return redirect('/');

    });

    /**
     * Get time from database
     */

    Route::get('task/time_{task}', function (Task $task) {
        return $task->showNormalTime($task->total_duration);
    });

    /**
     *  Close task
     */

    Route::post('/task/close_{task}', function (Task $task) {

        if( $task->is_active === 1 ){
            $task->session_duration =  abs( $task->time_started - time() );
            $task->total_duration += $task->session_duration;
            $task->is_active = 0;
            $task->is_done = 1;
        } else {
            $task->is_done = 1;
        }
        
        $task->update();

        return redirect('/');
    });

    /**
     *  Delete task
     */

    Route::delete('/profile/delete_{task}', function (Task $task) {
        $task->delete();

        return redirect('/profile');
    });
    
    /**
     *  Restore task
     */

    Route::post('/profile/restore_{task}', function (Task $task) {
        $task->is_done = 0;
        $task->update();

        return redirect('/profile');
    });

    /**
     *  Get time when page reloads
     */
    Route::post('/task/get_stopwatch_time_{task}', function(Task $task){
        $time = $task->calculateMissedTime($task->time_started);

       return $time;
    });


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


    Route::get('/profile', function (){
        $closedTasks = Task::orderBy('created_at', 'desc')
            ->where('id_user', '=', Auth::id())
            ->where('is_done', '=', '1')
            ->get();

        return view('/profile', [
            'tasks' => $closedTasks
        ]);
    });




});


