
@extends('layouts.app')
@section('content')
    <h3>Edit customer</h3>
    <div class="table-responsive">
        {!! Form::model($models[0], ['action' => ['CustomerController@update', $models[0]]]) !!}
        {!! Form::submit('Save changes',['class' => "btn btn-success"]); !!}
        <br />
        <br />
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
            <div class="text-danger col-md-8 field-validation-valid row" data-valmsg-for="{{$field}}" data-valmsg-replace="true"></div>
            @if($errors->first($field))
                <div class="alert alert-danger row">
                    {{ $errors->first($field) }}
                </div>
            @endif
            <div class="form-group row">
                {!! Form::label($field, ucfirst($field).':', ['class' => 'control-label col-md-4 col']) !!}
                @if($models[0]->withSelect($field))
                    {!! Form::select($field,$models[0]->withSelect($field),$models[0]->$field,['class' => 'col-md-6 col form-control', 'style' => 'padding: 1px 0 3px 10px;']) !!}
                @else
                    {!! Form::text($field, $models[0]->$field, $vattr->validationOptions($field, ['class' => 'col-md-6 col form-control'])) !!}
                @endif
            </div>
        @endforeach
        {!! Form::submit('Save changes',['class' => "btn btn-success"]); !!}
        {!! Form::close() !!}
        {!! $models->appends(\Request::except('page'))->render() !!}
    </div>
    @include('partials.client_validation')
@endsection