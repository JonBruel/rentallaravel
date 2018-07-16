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
    <div class="wrapper" style="background-color: #cccccc; min-height: 1000px">
        <div class="row">
            <div class="container" style="text-align: center">Test system - under construction!</div>
        </div>
        <nav class="navbar navbar-expand-md navbar-light row" style="border-style: solid; border-width: 0 0 2px 0 ; border-color: black">
            <div class="container">

                <button class="navbar-toggler collapsed" type="button" data-toggle="collapse" data-target="#menub" aria-controls="menub" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                @guest
                @else
                    @if(\Session::get('impersonate'))
                        <a class="nav-link" href="/impersonate/leave">Stop impersonation </a>
                    @endif
                @endguest

            <!-- Menu -->
                <div class="col navbar navbar-expand-md navbar-dark">
                    <nav class="collapse navbar-collapse" id="menub">



                        <?php $oldlevel = 1 ?>
                        @foreach(\Session::get('menuStructure') as $menupoint => $item)
                            <?php
                            $newlevel = $item['level'];
                            $strong = ($item['strenght'] == true)?'bold':'normal';
                            ?>
                            @if($newlevel < $oldlevel)
                                </ul>
                                    </div>
                            @endif

                            @if($newlevel == 1 and sizeof($item['childrenmap']) > 0)
                                <div class="dropdown">
                                    <button class="btn btn-dropdown dropdown-toggle" data-toggle="dropdown" style="margin-left: 3px">
                                        <span style="font-weight: {{$strong}}">{{ __($item['text']) }}</span><span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu">
                            @endif

                            @if($newlevel == 1 and sizeof($item['childrenmap']) == 0)
                                    <button class="btn btn-dropdown " style="margin-left: 3px">
                                       <a  style="color: black" href="/{{$item['path']}}"><span style="font-weight: {{$strong}}">{{ __($item['text']) }}</span></a>
                                    </button>
                            @endif

                            @if($newlevel == 2 and $item['show'] != 'select')
                                <li class="" onclick="window.location = '/{{$item['path']}}'">
                                    <a class="dropdown-item" href="/{{$item['path']}}" ><span style="font-weight: {{$strong}}">{{ __($item['text']) }}</span></a>
                                </li>
                            @endif

                            <?php $oldlevel = $newlevel ?>
                        @endforeach
                         </ul></div>

                    </nav>
                </div>

            <!-- Locale chooser, any existing query parameters are removed. It is possible to kkep them,
                 but not as a form, but as a window.location.href = 'url value'.
                 This will require that the url is calculated, possible but not elegant.
             -->
            <form id="selectLanguageForm" action="{{Session::get('sanitizedpath')}}" method="get">
                {{Form::select('culture', config('app.locales',[]), Session::get('culture'), array('id' => 'selectLanguage', 'onchange' => 'this.form.submit();'))}}
            </form>

            </div>
        </nav>

        <main class="container" style="margin-top: 5px">
            @if(\Session::get('warning'))
                <div class="alert alert-warning border border-primary"">{{\Session::get('warning')}}</div>
            @endif
            @if(\Session::get('success'))
                <div class="alert alert-success border border-primary"">{{\Session::get('success')}}</div>
            @endif

            <div class="row">




                <div class="col col-md-12">
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
    // Add hover effect to menus
    jQuery('ul.nav li.dropdown').hover(function() {
        jQuery(this).find('.dropdown-menu').stop(true, true).delay(200).fadeIn();
    }, function() {
        jQuery(this).find('.dropdown-menu').stop(true, true).delay(200).fadeOut();
    });
</script>

</html>
