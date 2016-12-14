<?php

namespace App\Http\Controllers;

use App\Task;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Db;

class TaskController extends Controller
{

    /**
     * Used to show all active tasks for current user.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */

    public function showUnclosedTasks(){
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
    }

    
    
    /**
     * Creates new tasks and validate it.
     *
     * @return Main page
     */

    public function createNewTask(Request $request){
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
    }

    
    
    /** Close task */
    
    public function closeTask(Task $task){
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
    }

    
   
    /** Restore task */
    
    public function restoreTask(Task $task){
        $task->is_done = 0;
        $task->update();

        return redirect('/profile');
    }
    
    
    
    /** Delete task */
    
    public function deleteTask(Task $task){
        $task->delete();

        return redirect('/profile');
    }
    
    
    
    /** Start timer for task */
    
    public function startTimer(Task $task){
        $task->time_started = time();
        $task->is_active = 1;
        $task->update();

        return redirect('/');
    }

    
    
    /** Stops timer for task */
    
    public function stopTimer(Task $task){
        $task->session_duration =  abs( $task->time_started - time() );
        $task->total_duration += $task->session_duration;
        $task->is_active = 0;
        $task->update();

        return redirect('/');
    }

    
    
    /** Update time that was spend on current task. Works after user clicked on 'Stop' button */
    
    public function updateFrontSpendTime(Task $task){
        return $task->showNormalTime($task->total_duration);
    }



    /** Update time of stopwatch when page is reloaded */

    public function updateTimeOnPageReload(Task $task){
        $time = $task->calculateMissedTime($task->time_started);

        return $time;
    }


}
