<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Quiz</title>
    <!-- Bootstrap -->
    <link href="{{ asset('node_modules/gentelella/vendors/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="{{ asset('node_modules/gentelella/vendors/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">
    <!-- NProgress -->
    <link href="{{ asset('node_modules/gentelella/vendors/nprogress/nprogress.css') }}" rel="stylesheet">
    <!-- Custom Theme Style -->
    <link href="{{ asset('node_modules/gentelella/build/css/custom.min.css') }}" rel="stylesheet">
	<link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <script src="https://kit.fontawesome.com/b734116fe6.js" crossorigin="anonymous"></script>

	@yield('style')

</head>

<body class="nav-md">
    <div class="container body">
        <div class="main_container" style="background-color: #e5e4e4">

			<div class="col-md-3 left_col">
                @include('backend.partials.sidebar-menu')
            </div>

			<!-- top navigation -->
{{--
            @include('backend.partials.top-menu')
--}}
            <!-- /top navigation -->

			<!-- page content -->
            <div class="right_col" role="main" style="background-color: #e5e4e4">
                <div class="">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>
    <!-- jQuery -->
    <script src="{{ asset('node_modules/gentelella/vendors/jquery/dist/jquery.min.js') }}"></script>
    <!-- Bootstrap -->
    <script src="{{ asset('node_modules/gentelella/vendors/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
    <!-- FastClick -->
    <script src="{{ asset('node_modules/gentelella/vendors/fastclick/lib/fastclick.js') }}"></script>
    <!-- NProgress -->
    <script src="{{ asset('node_modules/gentelella/vendors/nprogress/nprogress.js') }}"></script>
    <!-- Custom Theme Scripts -->
    <script src="{{ asset('node_modules/gentelella/build/js/custom.min.js') }}"></script>
	<script src="{{ asset('js/script.js') }}"></script>

	@yield('script')


</body>

</html>
