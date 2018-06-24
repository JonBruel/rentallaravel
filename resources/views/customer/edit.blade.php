
@extends('layouts.app')
@section('content')
    <h3>Edit customer</h3>
    <div class="table-responsive">
        {!! Form::model($model, ['action' => ['CustomerController@update', $model]]) !!}
        @foreach($fields as $field)
            <div class="row">
                {!! Form::label($field, ucfirst($field).':', ['class' => 'col-md-4 col']) !!}
                {!! Form::text($field, $model->$field, ['class' => 'col-md-6 col']) !!}
            </div>
        @endforeach
        {!! Form::submit('Save changes'); !!}
        {!! Form::close() !!}
    </div>
@endsection