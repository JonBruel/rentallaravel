@extends('layouts.app')
@section('content')
    <h3>{{__('Statistics')}}</h3>
    <div style="height: 1000px">
        <iframe src ="{{$awurl }}" frameborder="0" width="100%" height="99%">
            <p>{{ __('Your browser does not support iframes') }}.</p>
        </iframe>
    </div>
@endsection