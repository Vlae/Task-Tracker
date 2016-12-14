@extends('layouts.app')

@section('title', 'Vlae\'s time tracker')

@section('content')
<!-- Bootstrap Boilerplate... -->


    <!-- Display Validation Errors -->
@include('common.errors')
<!-- New Task Form -->
<div class="panel panel-default">
    <div class="panel-heading">
        @if( Auth::check() )
        <span class="fa fa-star-o fa-2x current-task-margin"> Current Task: </span>
        <span class="timer-task-name"></span>
        <div id="stopwatch" style="display: none">
            00:00:00
        </div>
        @else
            <img src="{{ asset('favicon.ico') }}" style="height:40px; width: 40px;">
            <span style="font-size:16px; padding: 4px;"> Task tracker</span>
        @endif

        <div class="registration">
            @if( Auth::check() )
                <ul class="hr">
                    <li> {{ Auth::user()->name }}</li>
                    <li class="fa fa-user-circle fa-2x"><a href="/profile"> Profile</a></li>
                    <li class="fa fa-sign-out fa-2x"><a href="/logout"> Logout </a></li>
                </ul>

            @else
                <ul class="hr">
                    <li class="fa fa-registered"><a href="/register"> Register </a></li>
                    <li class="fa fa-sign-in"><a href="/login"> Login </a></li>
                </ul>
            @endif
        </div>
    </div>

    <br>
    @if( Auth::check() )
    <hr>
    <form action="{{ url('task') }}" method="POST" class="form-horizontal">
    {!! csrf_field() !!}


    <!-- Task Name -->
        <div class="form-group">
            <label for="task" class="col-sm-3 control-label">Task</label>

            <div class="col-sm-6">
                <input type="text" name="name" id="task-name" class="form-control">
            </div>

            <div class="col-sm-3">
                <button type="submit" class="btn btn-success">
                    <i class="fa fa-plus"></i> Add Task
                </button>
            </div>
        </div>
    </form>
    <hr>
    @else
        <div class="container">
            <div class="alert alert-info" role="alert">
                For using this tracker you need to sign up or register. (Links in right top corner)
            </div>
        </div>
    @endif


<!-- Current Tasks -->
@if (count($tasks) > 0)
        <div class="panel-body">
            <table class="table table-striped task-table">

                <!-- Table Headings -->
                <thead>
                <th class="task-table-number">#</th>
                <th class="col-sm-5">Task</th>
                <th class="col-sm-3">Spended time:</th>
                <th class="col-sm-2">Start/Stop timer:</th>
                <th class="col-sm-2">Close task:</th>
                </thead>
                <!-- Table Body -->
                <tbody>

            @foreach ($tasks as $key=>$task)
                @if( $task->is_done == 0 )
                    <tr>
                        <td> {{ ++$key }}</td>
                        <!-- Task Name -->
                        <td class="table-text">
                            <span id="task-name-{{ $task->id }}" class="col-sm-11">{{ $task->name }}</span>
                        </td>
                        <!-- Show time of task -->
                        <td class="col-sm-3">
                            <span id="spended-time-{{ $task->id }}" class="col-sm-6 ">
                                    {{  $task->showNormalTime($task->total_duration)}}
                            </span>
                            {{--<span class="fa fa-plus-square-o" id="{{ $task->id }}" />--}}
                        </td>

                        <!-- Start/Stop timer --->
                        <td class="col-sm-2">
                            <!-- Start Timer -->
                            <form action="{{ url('task/start_'.$task->id) }}" method="POST">
                                {!! csrf_field() !!}
                                {!! method_field('POST') !!}

                                <button type="submit"
                                        id="start_{{$task->id}}"
                                        value="{{$task->id}}"
                                        class="btn btn-success ajax-btn-start"
                                        @if( $active_task == 1)
                                            style="display: none"
                                        @endif
                                >
                                    <i class="fa fa-play"></i> Start
                                </button>
                                <input type="hidden" name="_method" value="POST">
                                <input type="hidden" name="id" value="{{ $task->id }}">
                            </form>

                            <!-- Stop Timer -->
                            <form action="{{ url('task/stop_'.$task->id) }}" method="POST">
                                {!! csrf_field() !!}
                                {!! method_field('POST') !!}

                                <button type="submit"
                                        id="stop_{{$task->id}}"
                                        value="{{$task->id}}"
                                        @if( $active_task == 0 && $task->is_active == 0 || $active_task == 1 && $task->is_active == 0)
                                            style="display: none"
                                            class="btn btn-primary ajax-btn-stop"
                                        @else
                                            class="btn btn-primary ajax-btn-stop ajax-btn-stop-active"
                                        @endif
                                >
                                    <i class="fa fa-pause"></i> Stop
                                </button>
                                <input type="hidden" name="_method" value="POST">
                                <input type="hidden" name="id" value="{{ $task->id }}">
                                @if( $active_task == 1 && $task->is_active == 1 && $time > 0)
                                    <input type="hidden" name="time" value="{{  $time }}">
                                @endif
                            </form>

                        </td>

                        <!-- Delete Button -->
                        <td class="col-sm-2">
                            <form action="{{ url('task/close_'.$task->id) }}" method="POST">
                                {!! csrf_field() !!}
                                {!! method_field('DELETE') !!}

                                <button type="submit"
                                        id="delete_{{ $task->id }}"
                                        value="{{ $task->id }}"
                                        class="btn btn-danger ajax-btn-delete"
                                >
                                    <i class="fa fa-stop"></i> Close
                                </button>
                                <input type="hidden" name="_method" value="POST">
                                <input type="hidden" name="id" value="{{ $task->id }}">
                            </form>
                        </td>
                    </tr>
                @endif
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif


<script>
$(document).ready(function(){

    //check does page has unstopped task
    if ( $('button').hasClass('ajax-btn-stop-active') ) {

        // get name for task
        let taskId = $('.ajax-btn-stop-active').attr('value');
        let taskName = $('#task-name-' + taskId).html().substring(0, 65);
        if( taskName.length == 65)
                taskName += '...';
        $('.timer-task-name').html(taskName);

        // get time from hidden input
        let time = $('input[name=time]').attr('value');
        $('#stopwatch').stopwatch({startTime: time * 1000}).stopwatch('start').show();
    }


    $('.ajax-btn-start').stopwatch().click(function( event ){
        var id = $(this).attr('value');
        event.preventDefault();
        $.ajax({
            type: 'POST',
            url: './task/start_' + id,
            data: {
                '_token': '{{ csrf_token() }}'
            },
            success: function(){
                $('#stopwatch').stopwatch().stopwatch('destroy')
                        .stopwatch().stopwatch('reset')
                        .stopwatch('start').show();

                let taskName = $('#task-name-' + id).html().substring(0, 65);
                if( taskName.length == 65)
                    taskName += '...';
                $('.timer-task-name').html(taskName);

                $('.ajax-btn-start').hide();
                $('#stop_' + id).show();
            }
        });
    });

    $('.ajax-btn-stop').stopwatch().click(function( event ){
        var id = $(this).attr('value');
        event.preventDefault();
        $.ajax({
            type: 'POST',
            url: './task/stop_' + id,
            data: {
                '_token': '{{csrf_token()}}'
            },
            success: function(){
                $('#stopwatch').stopwatch().stopwatch('stop').stopwatch('reset');

                $('#spended-time-' + id).load('./task/time_' + id);
                $('.ajax-btn-stop').hide();
                $('.ajax-btn-start').show();

                console.log(id);
            }
        })
    });

    $('.ajax-btn-delete').bind('click', function() {
       if( !confirm('Close task?') )
               return false;
        else
           $('.timer-task-name').html('');
    });
});

// Google analytics

(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

ga('create', 'UA-88771120-1', 'auto');
ga('send', 'pageview');


</script>
@endsection