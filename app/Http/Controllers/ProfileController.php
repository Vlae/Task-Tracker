<?php

namespace App\Http\Controllers;

use App\Task;

use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{

    /** Shows closed tasks for current user */

    public function showProfileTasks(){
        $closedTasks = Task::orderBy('created_at', 'desc')
            ->where('id_user', '=', Auth::id())
            ->where('is_done', '=', '1')
            ->get();

        return view('/profile', [
            'tasks' => $closedTasks
        ]);
    }
}
