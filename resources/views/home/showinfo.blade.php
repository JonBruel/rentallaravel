@extends('layouts.app')
@section('content')
    <div class="table-responsive">
        <div class="alert alert-success" role="alert"  id="next_vacancy">
            {{ __('First vacant period') }}: {{$firstFree}}. <a href="/home/checkbookings?menupoint=10020">{{ __('Check vacancy') }}</a>
        </div>
        {!! $info !!}
    </div>
    <script>
        $("#next_vacancy").fadeTo(5000, 1500).slideUp(1500);
    </script>
@endsection
