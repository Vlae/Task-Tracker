<!-- resources/views/layouts/app.blade.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <title>@yield('title')</title>
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet" >
    <link href="{{ asset('bootstrap-3.3.7/css/bootstrap.min.css') }}" rel="stylesheet" >
   <link href="{{ asset('font-awesome-4.7.0/css/font-awesome.min.css') }}" rel="stylesheet" >
    <script src="{{ asset('js/jquery-3.1.1.js') }}"></script>
    <script src="{{ asset('js/jquery.stopwatch.js') }}"></script>
    <link rel="icon" href="{{ asset('favicon.ico') }}">

</head>

<body>

@yield('content')
</body>
</html>