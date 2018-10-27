
@extends('layouts.app')
@section('content')

    <h3>Edit testimonial</h3>
    <div class="table-responsive">
        {!! Form::model($testimonial, ['action' => ['HomeController@updatetestimonial', $testimonial]]) !!}
        {!! Form::submit(__('Save changes'),['class' => "btn btn-primary"]); !!}
        <br />
        <br />

        @include('partials.two_column_edit_1', ['model' => $testimonial, 'errors' => $errors, 'fields' => $fields, 'vattr' => $vattr])
        {!! Form::submit(__('Save changes'),['class' => "btn btn-primary"]); !!}
        {!! Form::close() !!}
    </div>

@include('partials.client_validation')
@endsection