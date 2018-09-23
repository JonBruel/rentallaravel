
@extends('layouts.app')
@section('content')
    <style>
        .form-group.row :nth-child(odd){
            #background-color: floralwhite;
        }
        .form-group.row :nth-child(even){
            #background-color: mintcream;
        }
    </style>
    <h3>{{ __('Batchtask for').' '.$models[0]->house->name }}</h3>
    <div class="table-responsive">
        {!! Form::model($models[0], ['action' => ['SetupController@updatebatchtask', $models[0]]]) !!}
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
        @if($lockbatch > 0)
            <div>
                <p><strong>{{ __('The automatic system for sending information has been stalled to prevent undesired emails or other automatic actions while you update the batchtasks') }}.</strong></p>
            </div>
            {!! Form::select('lockbatch',$lockbatchpossibilities,$lockbatch,['class' => 'col-md-11 col form-control', 'style' => 'padding: 1px 0 3px 10px;']) !!}
        @else
            <div>
                <p><strong>{{ __('The batchtask system is active for this house') }}</strong></p>
            </div>
        @endif

        <div class="form-group row row-striped">
            {!! Form::label('name', __('Name').':', ['class' => 'control-label col-md-4 col']) !!}
            {!! Form::text('name', $models[0]->name, ['class' => 'col-md-6 col form-control']) !!}
        </div>

        <div class="form-group row row-striped">
            {!! Form::label('active', __('Active').':', ['class' => 'control-label col-md-3 col']) !!}
            {!! Form::checkbox('active', 1, ($models[0]->active == 1), ['class' => 'col-md-1 col form-control', 'id' => 'active']) !!}
            {!! Form::text('activefrom', $models[0]->activefrom, ['class' => 'col-md-6 col form-control', 'id' => 'activefrom']) !!}
        </div>
        <?php $nextfields = ['posttypeid', 'emailid', 'batchfunctionid'] ?>
        @foreach($nextfields as $field)
            <div class="text-danger col-md-8 field-validation-valid row" data-valmsg-for="{{$field}}" data-valmsg-replace="true"></div>
            @if($errors->first($field))
                <div class="alert alert-danger row">
                    {{ $errors->first($field) }}
                </div>
            @endif
            <div class="form-group row row-striped">
                {!! Form::label($field, __(ucfirst($field)).':', ['class' => 'control-label col-md-4 col']) !!}
                @if($models[0]->withSelect($field))
                    {!! Form::select($field,$models[0]->withSelect($field),$models[0]->$field,['class' => 'col-md-6 col form-control', 'style' => 'padding: 1px 0 3px 10px;']) !!}
                @else
                    {!! Form::text($field, $models[0]->$field, ['class' => 'col-md-6 col form-control']) !!}
                @endif
            </div>
        @endforeach
        <?php $field = 'mailto'; $receptors = explode(',', $models[0]->$field); ?>
        <div class="form-group row row-striped">
            {!! Form::label('nofield',__('Mailto').':', ['class' => 'control-label col-md-12 col']) !!}
        </div>
        @foreach($models[0]->withSelect($field) as $key => $value)
            <div class="form-group row row-striped">
                {!! Form::label($field.'['.$key.']', __($value).':', ['class' => 'control-label offset-md-1 col-md-2 col ']) !!}
                {!! Form::checkbox($field.'['.$key.']', $key, (in_array($key, $receptors)), ['class' => 'col-md-1 col form-control']) !!}
            </div>
        @endforeach
        <?php $field = 'paymentbelow' ?>
        <div class="text-danger col-md-8 field-validation-valid row" data-valmsg-for="{{$field}}" data-valmsg-replace="true"></div>
        @if($errors->first($field))
            <div class="alert alert-danger row">
                {{ $errors->first($field) }}
            </div>
        @endif
        <div class="form-group row row-striped">
            {!! Form::label('usepaymentbelow', __('Paymentbelow').':', ['class' => 'control-label col-md-3 col']) !!}
            {!! Form::checkbox('usepaymentbelow', 1, ($models[0]->usepaymentbelow == 1), ['class' => 'col-md-1 col form-control', 'id' => 'usepaymentbelow']) !!}
            {!! Form::text('paymentbelow', $models[0]->paymentbelow, ['class' => 'col-md-6 col form-control', 'id' => 'paymentbelow']) !!}<br />

        </div>

        <div class="form-group row row-striped">
            <div class="col-md-6 offset-md-4">
                <span class="glyphicon glyphicon-arrow-up"></span>{{ __('You may use a negative ratio to fire if greater than the numeric value')}}.
            </div>
        </div>
        <div class="form-group row row-striped">
            {!! Form::label('userequiredposttypeid', __('Requiredposttypeid').':', ['class' => 'control-label col-md-3 col']) !!}
            {!! Form::checkbox('userequiredposttypeid', 1, ($models[0]->userequiredposttypeid == 1), ['class' => 'col-md-1 col form-control', 'id' => 'userequiredposttypeid']) !!}
            {!! Form::select('requiredposttypeid', $models[0]->withSelect('requiredposttypeid'), $models[0]->requiredposttypeid, ['class' => 'col-md-6 col form-control', 'id' => 'requiredposttypeid', 'style' => 'padding: 1px 0 3px 10px;']) !!}
        </div>
        <div class="form-group row row-striped">
            {!! Form::label('usetimedelaystart', __('Timedelaystart').':', ['class' => 'control-label col-md-3 col']) !!}
            {!! Form::checkbox('usetimedelaystart', $models[0]->usetimedelaystart, ($models[0]->usetimedelaystart == 1), ['class' => 'col-md-1 col form-control', 'id' => 'usetimedelaystart']) !!}
            {!! Form::text('timedelaystart', $models[0]->timedelaystart, ['class' => 'col-md-6 col form-control', 'id' => 'timedelaystart']) !!}
        </div>
        <div class="form-group row row-striped">
            {!! Form::label('usetimedelayfrom', __('Timedelayfrom').':', ['class' => 'control-label col-md-3 col']) !!}
            {!! Form::checkbox('usetimedelayfrom', 1, ($models[0]->usetimedelayfrom == 1), ['class' => 'col-md-1 col form-control', 'id' => 'usetimedelayfrom']) !!}
            {!! Form::text('timedelayfrom', $models[0]->timedelayfrom, ['class' => 'col-md-6 col form-control', 'id' => 'timedelayfrom']) !!}
        </div>
        <div class="form-group row row-striped">
            <div class="col-md-6 offset-md-4">
                <span class="glyphicon glyphicon-arrow-up"></span>{{ __('You may use a negative days to fire if after the rental period')}}.
            </div>
        </div>
        <div class="form-group row row-striped">
            {!! Form::label('useaddposttypeid', __('Addposttypeid').':', ['class' => 'control-label col-md-3 col']) !!}
            {!! Form::checkbox('useaddposttypeid', 1, ($models[0]->useaddposttypeid == 1), ['class' => 'col-md-1 col form-control', 'id' => 'useaddposttypeid']) !!}
            {!! Form::select('addposttypeid', $models[0]->withSelect('addposttypeid'), $models[0]->addposttypeid, ['class' => 'col-md-6 col form-control', 'id' => 'addposttypeid', 'style' => 'padding: 1px 0 3px 10px;']) !!}
        </div>
        <div class="form-group row row-striped">
            {!! Form::label('usedontfireifposttypeid', __('Dontfireifposttypeid').':', ['class' => 'control-label col-md-3 col']) !!}
            {!! Form::checkbox('usedontfireifposttypeid', 1, ($models[0]->usedontfireifposttypeid == 1), ['class' => 'col-md-1 col form-control', 'id' => 'usedontfireifposttypeid']) !!}
            {!! Form::select('dontfireifposttypeid', $models[0]->withSelect('dontfireifposttypeid'), $models[0]->dontfireifposttypeid, ['class' => 'col-md-6 col form-control', 'id' => 'dontfireifposttypeid', 'style' => 'padding: 1px 0 3px 10px;']) !!}
        </div>
        <br />

        {!! Form::hidden('id', $models[0]->id) !!}
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