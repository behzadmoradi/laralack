<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <!-- Scripts -->
    <!-- <script src="{{ asset('js/app.js') }}" defer></script> -->
    <script src="{{ asset('js/pusher.js') }}" defer></script>
    <script src="js/jquery-3.4.1.min.js"></script>
    <script src="js/jquery.scrollbar.min.js"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}" defer></script>
    <script src="{{ asset('js/fontawesome.all.min.js') }} "></script>
    <script src="js/markdown-editor.js" defer></script>
    <script src="js/markdown-editor-parser.js" defer></script>
    <script src="js/api/api.js"></script>
    <!-- <script src="js/jquery.slimscroll.min.js"></script> -->
    <script src="js/api/profile.js" defer></script>
    <script src="js/api/channel.js" defer></script>
    <script src="js/api/invitation.js" defer></script>
    <script src="js/api/chat.js" defer></script>
    <script src="js/websockets/is-typing.js" defer></script>
    <script src="js/websockets/online-users.js" defer></script>
    <!-- https://github.com/Johann-S/bs-custom-file-input -->
    <script src="js/bs-custom-file-input.min.js"></script>
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <!-- Styles -->
    <!-- <link href="{{ asset('css/app.css') }}" rel="stylesheet"> -->
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/bootstrap-overrides.css') }}" rel="stylesheet">
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
    <link href="{{ asset('css/fontawesome.all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/markdown-editor.css') }}" rel="stylesheet">
    <link href="{{ asset('css/custom-scrollbar.css') }}" rel="stylesheet">
</head>

<script>
    $(document).ready(function() {
        $('.custom-scrollbar').scrollbar();
        $('.toast').toast('show');
        bsCustomFileInput.init();


        // https://github.com/rochal/jQuery-slimScroll
        // $('.custom-scrollbar').slimscroll({
        //     height: 'auto'
        // }).parent().css({
        //     'float': 'left',
        //     'margin-right': '0px'
        // });
    });
</script>
<body>
    @yield('content')
</body>
</html>
