
@extends('layouts.app')
@section('content')
    <style>
        body {
            padding : 10px ;

        }

        #exTab1 .tab-content {
            color : white;
            background-color: #428bca;
            padding : 5px 15px;
        }


        #exTab1 .nav-pills > li > a {
            border-radius: 4px;
        }


    </style>
    <h3>{{ __('Standardemail  for').' '.$models[0]->house->name }}</h3>
    <div class="table-responsive">
        {!! Form::model($models[0], ['action' => ['SetupController@updatestandardemail', $models[0]]]) !!}
        {!! Form::submit(__('Save changes'),['class' => "btn btn-success"]); !!}
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
        <br />

        <div class="form-group row">
            {!! Form::label('description', __('Description').':', ['class' => 'control-label col-md-2 col']) !!}
            {!! Form::text('descriptionI18n', __($models[0]->description), ['class' => 'col-md-6 col form-control']) !!}
        </div>
        <div id="exTab1" clasxs="container">
            <ul class="nav nav-pills">
                @foreach($standardemailcontents as $key => $standardemailcontent)
                    <li class="nav-item"><a class="nav-link {{($key=='da_DK')?'active':''}}" href="#section{{$key}}"  role="tab" data-toggle="tab">{{$key}}</a></li>
                @endforeach
            </ul>
            <div class="tab-content">
                @foreach($standardemailcontents as $key => $standardemailcontent)
                    <div id="section{{$key}}" class="tab-pane {{($key=='da_DK')?'active':''}}">
                        <div class="form-group row">
                            {!! Form::textarea('contents['.$key.']', $standardemailcontent, ['class' => 'col-md-12 col form-control', 'style' => 'margin: 5px; max-width: 99%']) !!}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <br />

        {!! Form::hidden('id', $models[0]->id) !!}
        {!! Form::hidden('description', $models[0]->description) !!}
        {!! Form::hidden('houseid', $models[0]->houseid) !!}
        {!! Form::hidden('ownerid', $models[0]->ownerid) !!}
        {!! Form::submit(__('Save changes'),['class' => "btn btn-success"]); !!}
        {!! Form::close() !!}
        <br />
        {!! $models->appends(\Request::except('page'))->render() !!}
    </div>
    @include('partials.client_validation')
@endsection
@section('scripts')
    <link href="{{ asset('/vendor/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css', true)}}" rel="stylesheet">
    <script type="text/javascript" src="{{ asset('/vendor/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js', true)}}"></script>
    <script type="text/javascript">
        $(function() {
            $('#activefrom').datetimepicker({ language: '{{str_replace('_', '-', App::getLocale())}}',
                format: 'yyyy-mm-dd',
                autoclose: true,
                minView: 1,
                startView: 3});
        });
    </script>
@endsection