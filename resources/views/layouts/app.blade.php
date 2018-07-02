<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts-->
    <script src="{{ asset('js/app.js', true) }}" ></script>

    <!-- Styles-->
    <link href="{{ asset('css/app.css', true) }}" rel="stylesheet">


</head>
<body>
    <div class="wrapper">
        <nav class="navbar navbar-expand-md navbar-light row">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>

                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto pull-right row">
                        <!-- Authentication Links -->
                        @guest
                            <li type="button" class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                            </li>
                        @else
                            <li class="nav-item dropdown">
                            @if(\Session::get('impersonate'))
                                <a class="nav-link" href="/impersonate/leave">Stop impersonation </a>
                            @endif
                            </li>

                            <li class="nav-item dropdown">

                                <a id="navbarDropdown" class="nav-link dropdown-toggle col" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}<span class="caret"></span>
                                </a>

                                <div class="dropdown-menu dropdown-menu-right col" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="container">
            <div role="navigation" class="navbar navbar-expand-md navbar-light row">
                <div col-md-2 class="col align-self-start">
                    <!-- Locale chooser, any existing query parameters are removed. It is possible to kkep them,
                         but not as a form, but as a window.location.href = 'url value'.
                         This will require that the url is calculated, possible but not elegant.
                     -->
                    <div class="form-group">
                        <form id="selectLanguageForm" action="{{Session::get('sanitizedpath')}}" method="get">
                            {{Form::select('culture', config('app.locales',[]), Session::get('culture'), array('id' => 'selectLanguage', 'onchange' => 'this.form.submit();'))}}
                        </form>
                    </div>


                <!-- Menu -->
                    <button class="navbar-toggler collapsed" type="button" data-toggle="collapse" data-target="#menub" aria-controls="menub" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div id="menu">
                        @foreach(\Session::get('menuStructure') as $menupoint => $item)
                            @if(array_key_exists('level', $item))
                                @if($item['level'] == 1)
                                    <div class="{{$item['cssclass']}}" style="float: right" onclick="window.location = '/{{$item['path']}}'">
                                        <a href="/{{$item['path']}}">@lang($item['text'])</a><br />
                                    </div>
                                @endif
                                @if($item['level'] != 1)
                                    @if($item['show'] == 'show')
                                        <div class="{{$item['cssclass']}}" style="float: right" onclick="window.location = '/{{$item['path']}}'">
                                            <a href="/{{$item['path']}}">@lang($item['text'])</a><br />
                                        </div>
                                    @endif
                                @endif
                           @endif
                        @endforeach
                    </div>


                </div>
                <div class="col-md-10 col">
                    <?php echo $__env->yieldContent('content'); ?>
                    <br />
                    Time lapse: {{\Session::get('ost')}}
                    <br />
                        Culture: {{\Session::get('culture')}}
                    <br />
                    Uri: {{\Session::get('uri')}}
                    <br />
                        sanitizedpath: {{\Session::get('sanitizedpath')}}

                        <br />
                        App locale: {{App::getLocale()}}
                </div>

            </div>
        </main>
    </div>
</body>

<script >
</script>

</html>
