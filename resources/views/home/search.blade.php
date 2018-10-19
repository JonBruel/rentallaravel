@extends('layouts.app')
@section('content')
    <div id="vacancyPrMonth" style="text-align: center">
        @include('partials.search', ['houseid' => session('defaultHouse' , 1)])
    </div>
@endsection
