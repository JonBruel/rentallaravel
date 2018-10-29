@extends('layouts.app')
@section('content')
    <h3>{{__('Insert new customer')}}</h3>
    <div class="table-responsive table-sm">
        {!! Form::model($models[0], ['action' => ['CustomerController@store', $models[0]]]) !!}
        {!! Form::submit(__('Create'),['class' => "btn btn-primary"]); !!}
        <br />
        <br />
        @include('partials.two_column_edit_1', ['model' => $models[0], 'errors' => $models[0]->getErrors(), 'fields' => $fields, 'vattr' => $vattr])
        {!! Form::submit(__('Create'),['class' => "btn btn-primary"]); !!}
        {!! Form::close() !!}
    </div>
    @include('partials.client_validation')
@endsection