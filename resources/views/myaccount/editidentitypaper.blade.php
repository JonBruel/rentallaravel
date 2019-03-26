
@extends('layouts.app')
@section('content')
    <h3>{{__('Edit guest passport details')}}</h3>
    <div class="table-responsive table-sm">
        {!! Form::model($models[0], ['action' => ['MyAccountController@update', $models[0]]]) !!}
        {!! Form::submit(__('Save changes'),['class' => "btn btn-primary"]); !!}
        <br />
        <br />

        @include('partials.two_column_edit_1', ['model' => $models[0], 'errors' => $errors, 'fields' => $fields, 'vattr' => $vattr])
        {!! Form::submit(__('Save changes'),['class' => "btn btn-primary"]); !!}
        {!! Form::close() !!}
    </div>
    @include('partials.client_validation')
@endsection
@section('scripts')
    <link href="{{ asset('/vendor/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css', config('app.secure', false))}}" rel="stylesheet">
    <script type="text/javascript" src="{{ asset('/vendor/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js', config('app.secure', false))}}"></script>
    <script type="text/javascript">
        culture = "{{App::getLocale()}}";
        culture = culture.replace("_", "-");

        //$(document).ready(getWeeks(0));
        $(function() {
            $('#dateofissue').datetimepicker({ language: '{{str_replace('_', '-', App::getLocale())}}',
                format: 'dd-mm-yyyy',
                autoclose: true,
                minView: 2,
                maxView: 4,
                startView: 4});

            $('#dateofbirth').datetimepicker({ language: '{{str_replace('_', '-', App::getLocale())}}',
                format: 'dd-mm-yyyy',
                autoclose: true,
                minView: 2,
                maxView: 4,
                startView: 4});
        });
    </script>
@endsection
