
@extends('layouts.app')
@section('content')
    <h3>{{__('Not implemented')}}</h3>
    <div class="alert alert-warning border border-primary">{{ __('The function is not implemented in the present version.') }}</div>
    @include('partials.client_validation')
@endsection