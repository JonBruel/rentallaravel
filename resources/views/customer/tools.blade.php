
@extends('layouts.app')
@section('content')
    <h3>{{__('Advanced customer functions')}}</h3>
    <br /><br />
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
            <tr>
                <th><a href="/customer/merge" class="btn btn-primary" role="button">{{ __('Merge customers') }}</a></th>
                <th><a href="/contract/listmails/{{$id}}" class="btn btn-primary" role="button">{{ __('Check mails') }}</a></th>
                <th><a href="/customer/checkaccount/{{$id}}" class="btn btn-primary" role="button">{{ __('Check account') }}</a></th>
            </tr>
            </thead>
        </table>
    @include('partials.client_validation')
@endsection