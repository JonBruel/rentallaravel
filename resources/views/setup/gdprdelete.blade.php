
@extends('layouts.app')
@section('content')
    <h3>{{__('Delete customer information not required')}}</h3>
    <div class="alert alert-warning border border-primary" id="feedback">{{ __('Awaiting response from command:gdprdelete') }}</div>
@endsection
@section('scripts')
    <link href="{{ asset('/vendor/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css', true)}}" rel="stylesheet">
    <script type="text/javascript" src="{{ asset('/vendor/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js', config('app.secure', false))}}"></script>
    <script type="text/javascript">

        handle = setInterval(getText, 100);

        function getText()
        {
            url = '/ajax/getgdprdeletestatus' ;
            new $.getJSON(url, function(information)
            {
                content = information.text;
                $('#feedback').html(content);
                if (content.includes('Finished')) clearInterval(handle);
            });
        }
    </script>
@endsection