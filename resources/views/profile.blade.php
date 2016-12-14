@extends('layouts.app')

@section('title', 'Profile')

@section('content')
{{--<div class="container" style="background-color: lavender">--}}
<div class="panel panel-default">

@if( Auth::check() )
    <div class="panel-heading" >

        <div class="breadscrumb">
            <span class="fa fa-home fa-2x"><a href="/"> Home </a></span>
            <span>> Profile</span>
        </div>

        <div class="registration">
            <ul class="hr">
                <li> {{ Auth::user()->name }}</li>
                <li class="fa fa-list-alt fa-2x"><a href="/"> Tasks </a></li>
                <li class="fa fa-sign-out fa-2x"><a href="/logout"> Logout </a></li>
            </ul>
        </div>
    </div>



    @if (count($tasks) > 0)
        <div class="panel-body">
            <table class="table table-striped task-table">

                <!-- Table Headings -->
                <thead>
                <th class="task-table-number">#</th>
                <th class="col-sm-5">Task</th>
                <th class="col-sm-3">Spended time:</th>
                <th class="col-sm-2">Restore task:</th>
                <th class="col-sm-2">Delete task:</th>
                </thead>
                <!-- Table Body -->
                <tbody>

                @foreach ($tasks as $key=>$task)
                    <tr>
                        <td>
                            {{ ++$key }}
                        </td>

                        <!-- Task Name -->
                        <td class="table-text">
                            <span id="task-name-{{ $task->id }}">{{ $task->name }}</span>
                        </td>
                        <!-- Show time of task -->
                        <td>
                            <span id="spended-time-{{ $task->id }}">
                                {{  $task->showNormalTime($task->total_duration)}}
                            </span>
                        </td>

                        <!-- Restore Button -->
                        <td>
                            <form action="{{ url('profile/restore_'.$task->id) }}" method="POST">
                                {!! csrf_field() !!}

                                <button type="submit"
                                        id="restore_{{ $task->id }}"
                                        value="{{ $task->id }}"
                                        class="btn btn-primary"
                                >
                                    <i class="fa fa-reply"></i> Restore
                                </button>
                                <input type="hidden" name="_method" value="POST">
                                <input type="hidden" name="id" value="{{ $task->id }}">
                            </form>
                        </td>

                        <!-- Delete Button -->
                        <td>
                            <form action="{{ url('profile/delete_'.$task->id) }}" method="POST">
                                {!! csrf_field() !!}
                                {!! method_field('DELETE') !!}

                                <button type="submit"
                                        id="restore_{{ $task->id }}"
                                        value="{{ $task->id }}"
                                        class="btn btn-warning btn-delete"
                                >
                                    <i class="fa fa-trash"></i> Delete
                                </button>
                                <input type="hidden" name="_method" value="DELETE">
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @else
        You doesn't close any task.
    @endif

@else

    <div class="container">
        <div class="alert alert-info" role="alert">
           <h4> For using this tracker you need to sign up or register. (Links in right top corner)</h4>
                <ul class="hr">
                    <li><a href="/register"> Register </a></li>
                    <li><a href="/login"> Login </a></li>
                </ul>
        </div>
    </div>
@endif
</div>

<script>
    $(document).ready(function(){
        $('.btn-delete').bind('click', function () {
            if( !confirm('Delete task?') )
                return false;
        })
    });


</script>


@endsection