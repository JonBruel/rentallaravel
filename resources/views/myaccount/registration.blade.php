
@extends('layouts.app')
@section('content')
    <h3>{{__('My data')}}</h3>
    <div class="table-responsive">
        {!! Form::model($models[0], ['action' => ['MyAccountController@updateregistration', $models[0]]]) !!}
        {!! Form::submit(__('Save changes'),['class' => "btn btn-primary col col-md-12"]); !!}

        <br />
        <br />

        @include('partials.two_column_edit_1', ['model' => $models[0], 'errors' => $errors, 'fields' => $fields, 'vattr' => $vattr])
        {!! Form::hidden('id', $models[0]->id) !!}
        {!! Form::submit(__('Save changes'),['class' => "btn btn-primary col col-md-12"]); !!}
        {!! Form::close() !!}

    </div>
    <br /><br />
    @if($allowdelete)
        {!! Form::model($models[0], ['action' => ['MyAccountController@destroycustomer', $models[0]->id]]) !!}
        {!! Form::submit(__('Delete me'),['class' => "btn btn-primary col col-md-12", 'name' => 'delete', 'onclick' => 'thisisanerror()']); !!}
        {!! Form::close() !!}
    @endif
    @include('partials.client_validation')
@endsection