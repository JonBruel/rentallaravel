<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ __(config('app.name', 'Laravel')) }}</title>

    <!-- Scripts-->
    <script src="{{ asset('js/app.js', true) }}" ></script>

    <!-- Styles-->
    <link href="{{ asset('css/app.css', true) }}" rel="stylesheet">


</head>
<body>
<div class="wrapper">
    <nav class="navbar navbar-expand-md navbar-light row">
        <div class="container">

            <button class="navbar-toggler collapsed" type="button" data-toggle="collapse" data-target="#menub" aria-controls="menub" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Locale chooser, any existing query parameters are removed. It is possible to kkep them,
                 but not as a form, but as a window.location.href = 'url value'.
                 This will require that the url is calculated, possible but not elegant.
             -->

            <form id="selectLanguageForm" action="{{Session::get('sanitizedpath')}}" method="get">
                {{Form::select('culture', config('app.locales',[]), Session::get('culture'), array('id' => 'selectLanguage', 'onchange' => 'this.form.submit();'))}}
            </form>


            @guest
            @else
                @if(\Session::get('impersonate'))
                    <a class="nav-link" href="/impersonate/leave">Stop impersonation </a>
                @endif
            @endguest

        </div>
    </nav>

    <main class="container">
        @if(\Session::get('warning'))
            <div class="alert alert-warning border border-primary"">{{\Session::get('warning')}}</div>
@endif
@if(\Session::get('success'))
    <div class="alert alert-success border border-primary"">{{\Session::get('success')}}</div>
@endif

<div class="row">

    <div  class="col col-md-2" style="padding-left: 0">
        <!-- Menu -->
        <div class="col navbar navbar-expand-md navbar-light">
            <nav class="collapse navbar-collapse" id="menub">
                <div class="nav navbar-nav" >
                    @foreach(\Session::get('menuStructure') as $menupoint => $item)
                        @if(array_key_exists('level', $item))
                            @if($item['level'] == 1 or $item['show'] == 'show')
                                <div role="presentation" class="{{$item['cssclass']}} " onclick="window.location = '/{{$item['path']}}'">
                                    <a href="/{{$item['path']}}">@lang($item['text'])</a><br />
                                </div>
                            @endif
                        @endif
                    @endforeach
                </div>
            </nav>
        </div>

    </div>



    <div class="col col-md-10">
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
