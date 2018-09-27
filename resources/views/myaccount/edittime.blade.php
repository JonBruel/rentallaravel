@extends('layouts.app')
@section('content')
    <h3>{{__('My arrival and departure times')}}</h3>
    <div class="table-responsive">
        {!! Form::model($models[0], ['action' => ['MyAccountController@updatetime', $models[0]]]) !!}
        {!! Form::submit(__('Save changes'),['class' => "btn btn-primary"]); !!}
        <br /><br />
        <table class="table table-striped">
            @foreach($models as $model)
                <tr>
                    <td colspan="2">
                        <strong>{{ $model->contract->house->name }}:</strong> {{__('Period')}}: {{ $model->contract->getPeriodtext(\App::getLocale()) }}. {{__('Final price')}}; {{ $model->contract->finalprice }}
                    </td>
                </tr>
                <tr>
                    <td>{!! Form::label('landingdatetime_'.$model->id, __(ucfirst('landingdatetime')).':', ['class' => 'col-md-11']) !!}</td>
                    <td>{!! Form::text('landingdatetime_'.$model->id, $model->contract->landingdatetime, $vattr->validationOptions('landingdatetime', ['id' => 'landingdatetime_'.$model->id, 'class' => 'form-control col-md-11', 'style' =>"height: 28px"])) !!}</td>
                </tr>
                <tr>
                    <td>{!! Form::label('departuredatetime_'.$model->id, __(ucfirst('departuredatetime')).':', ['class' => 'col-md-11']) !!}</td>
                    <td>{!! Form::text('departuredatetime_'.$model->id, $model->contract->departuredatetime, $vattr->validationOptions('departuredatetime', ['id' => 'departuredatetime_'.$model->id, 'class' => 'form-control col-md-11', 'style' =>"height: 28px"])) !!}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        {!! Form::close() !!}
    </div>
@endsection
@section('scripts')
    <link href="{{ asset('/vendor/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css', true)}}" rel="stylesheet">
    <script type="text/javascript" src="{{ asset('/vendor/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js', true)}}"></script>
    <script type="text/javascript">
        $(function() {
            @foreach($models as $model)
                $('#landingdatetime_{{$model->id}}').datetimepicker({ language: '{{str_replace('_', '-', App::getLocale())}}',
                    format: 'yyyy-mm-dd hh:ii',
                    minuteStep: 30,
                    autoclose: true,
                    startView: 1});

                $('#departuredatetime_{{$model->id}}').datetimepicker({ language: '{{str_replace('_', '-', App::getLocale())}}',
                    format: 'yyyy-mm-dd hh:ii',
                    minuteStep: 30,
                    autoclose: true,
                    startView: 1});
            @endforeach
        });
    </script>
@endsection