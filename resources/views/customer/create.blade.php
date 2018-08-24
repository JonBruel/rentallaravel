@extends('layouts.app')
@section('content')
    <h3>Insert new customer</h3>
    <div class="table-responsive">
        {!! Form::model($models[0], ['action' => ['CustomerController@store', $models[0]]]) !!}
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
                {!! Form::text($field, $models[0]->$field, $vattr->validationOptions($field, ['class' => 'col-md-6 col form-control'])) !!}
            </div>
        @endforeach
        {!! Form::submit('Save changes',['class' => "btn btn-success"]); !!}
        {!! Form::close() !!}
    </div>
    @include('partials.client_validation')
@endsection