<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Raleway:300,400,600" rel="stylesheet" type="text/css">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/tailwind.css') }}" rel="stylesheet">
    {{--<link href="https://cdn.jsdelivr.net/npm/tailwindcss/dist/tailwind.min.css" rel="stylesheet">--}}
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css" integrity="sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp" crossorigin="anonymous">



</head>
<body>
<div id="app">

    <nav class="flex items-center justify-between flex-wrap bg-indigo-darker p-6">
        <div class="flex items-center flex-no-shrink text-white mr-6">
            <a class="text-white no-underline" href="{{ url('/') }}">
                <span class="font-semibold text-xl tracking-tight">LaraGala</span>
            </a>
        </div>
        <div class="block lg:hidden">
            <button class="flex items-center px-3 py-2 border rounded text-indigo-lightest border-indigo-light hover:text-white hover:border-white">
                <svg class="fill-current h-3 w-3" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><title>Menu</title><path d="M0 3h20v2H0V3zm0 6h20v2H0V9zm0 6h20v2H0v-2z"/></svg>
            </button>
        </div>
        <div class="w-full block flex-grow lg:flex lg:items-center lg:w-auto">
            <div class="text-sm lg:flex-grow">
                <a href="#in_development" class="block mt-4 lg:inline-block lg:mt-0 text-indigo-lightest hover:text-white mr-4">
                    Docs
                </a>
            </div>
            <div>
                @guest
                    <a href="{{ route('login') }}" class="inline-block text-sm px-4 py-2 leading-none border rounded text-white border-white hover:border-transparent hover:text-indigo hover:bg-white mt-4 lg:mt-0">{{ __('Login') }}</a>
                    <a href="{{ route('register') }}" class="inline-block text-sm px-4 py-2 leading-none border rounded text-white border-white hover:border-transparent hover:text-indigo hover:bg-white mt-4 lg:mt-0">{{ __('Register') }}</a>
                @else

                    <a href="{{ route('logout') }}" class="inline-block text-sm px-4 py-2 leading-none border rounded text-white border-white hover:border-transparent hover:text-indigo hover:bg-white mt-4 lg:mt-0" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">{{ __('Logout') }}</a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                @endguest
            </div>
        </div>
    </nav>

    {{--<nav class="navbar navbar-expand-md navbar-light navbar-laravel">--}}
        {{--<div class="container">--}}
            {{--<a class="navbar-brand" href="{{ url('/') }}">--}}
                {{--LaraGala--}}
            {{--</a>--}}
            {{--<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">--}}
                {{--<span class="navbar-toggler-icon"></span>--}}
            {{--</button>--}}

            {{--<div class="collapse navbar-collapse" id="navbarSupportedContent">--}}
                {{--<!-- Left Side Of Navbar -->--}}
                {{--<ul class="navbar-nav mr-auto">--}}

                {{--</ul>--}}

                {{--<!-- Right Side Of Navbar -->--}}
                {{--<ul class="navbar-nav ml-auto">--}}
                    {{--<!-- Authentication Links -->--}}

                    {{--<li><a class="nav-link" href=""></a></li>--}}
                    {{--<li><a class="nav-link" href=""></a></li>--}}

                {{--</ul>--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--</nav>--}}

    <main class="py-4">
        @yield('content')
    </main>
</div>
</body>
</html>
