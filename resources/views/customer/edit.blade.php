
@extends('layouts.app')
@section('content')
    <h3>{{__('Edit customer')}}</h3>
    <div class="table-responsive table-sm">
        {!! Form::model($models[0], ['action' => ['CustomerController@update', $models[0]]]) !!}
        {!! Form::submit(__('Save changes'),['class' => "btn btn-primary"]); !!}
        <br />
        <br />

        @include('partials.two_column_edit_1', ['model' => $models[0], 'errors' => $errors, 'fields' => $fields, 'vattr' => $vattr])
        {!! Form::submit(__('Save changes'),['class' => "btn btn-primary"]); !!}
        {!! Form::close() !!}
        {!! $models->appends(\Request::except('page'))->render() !!}
    </div>
    @include('partials.client_validation')
@endsection