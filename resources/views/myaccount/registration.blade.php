
@extends('layouts.app')
@section('content')
    <h3>{{__('My data')}}</h3>
    <div class="table-responsive">
        {!! Form::model($models[0], ['action' => ['MyAccountController@updateregistration', $models[0]]]) !!}
        {!! Form::submit(__('Save changes'),['class' => "btn btn-primary"]); !!}
        <br />
        <br />

        @include('partials.two_column_edit_1', ['model' => $models[0], 'errors' => $errors, 'fields' => $fields, 'vattr' => $vattr])
        {!! Form::hidden('id', $models[0]->id) !!}
        {!! Form::submit(__('Save changes'),['class' => "btn btn-primary"]); !!}
        {!! Form::close() !!}

    </div>
    @include('partials.client_validation')
@endsection