
@extends('layouts.app')
@section('content')
    <h3>Edit house</h3>
    <div class="table-responsive">
        {!! Form::model($model, ['action' => ['HouseController@update', $model]]) !!}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @foreach($fields as $field)
            <div class="row">
                {!! Form::label($field, ucfirst($field).':', ['class' => 'col-md-4 col']) !!}
                {!! Form::text($field, is_numeric($model->$field)?$model->$field:$model->$field, ['class' => 'col-md-6 col']) !!}
            </div>
        @endforeach
        {!! Form::submit('Save changes'); !!}
        {!! Form::close() !!}
    </div>
@endsection