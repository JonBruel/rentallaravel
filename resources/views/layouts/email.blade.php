<!DOCTYPE html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Styles-->
    <!-- Changes 15-11-2020 <link href="{{ asset('css/app.css', true) }}" rel="stylesheet"> -->
    <link href="cangeroni.hasselbalch.com/css/app.css" rel="stylesheet">
</head>
<body style="background-color: #ffffff;">
    <div class="wrapper" style="background-color: #ffffff;">
        <main class="container" style="margin-top: 5px">
            <div class="row">
                <div class="col col-md-12">
                    @yield('content')
                </div>
            </div>
        </main>
    </div>
</body>
</html>
