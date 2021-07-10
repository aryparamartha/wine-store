<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title','Winery App')</title>

    <link rel="shortcut icon" href="{{asset('assets/images/favicon.png')}}" />
    <!-- core:css -->
    <link rel="stylesheet" href="{{asset('assets/vendors/core/core.css')}}">
    <!-- endinject -->

    <!-- plugin css for this page -->
    @yield('plugin-css')
    <!-- end plugin css for this page -->

    <!-- inject:css -->
    <link rel="stylesheet" href="{{asset('assets/fonts/feather-font/css/iconfont.css')}}">
    <link rel="stylesheet" href="{{asset('assets/vendors/material-icon/materialdesignicons.css')}}">
    <!-- endinject -->

    <!-- Layout styles -->
    <link rel="stylesheet" href="{{asset('assets/css/style.css')}}">
    <!-- End layout styles -->

    <!-- custom css for this page -->
    @yield('custom-css')
    <!-- end custom css for this page -->
</head>

<body class="sidebar-dark">
    <div class="main-wrapper">
        <!-- SIDEBAR -->
        @include('layouts.admin.sidebar')
        <!-- SIDEBAR -->

        <div class="page-wrapper">
            <!-- NAVBAR -->
            @include('layouts.admin.navbar')
            <!-- NAVBAR -->

             <!-- NAVBAR -->
             @yield('content')
             <!-- NAVBAR -->

            <!-- FOOTER -->
            @include('layouts.admin.footer')
            <!-- FOOTER -->

        </div>
    </div>

    <!-- core:js -->
    <script src="{{asset('assets/vendors/core/core.js')}}"></script>
    <!-- endinject -->

    <!-- plugin js for this page -->
    @yield('plugin-js')
    <!-- end plugin js for this page -->

    <!-- inject:js -->
    <script src="{{asset('assets/vendors/feather-icons/feather.min.js')}}"></script>
    <script src="{{asset('assets/js/template.js')}}"></script>
    <!-- endinject -->

    <!-- custom js for this page -->
    @yield('custom-js')
    <!-- end custom js for this page -->
</body>
</html>