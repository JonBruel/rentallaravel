@extends('layouts.app')
<?php
use Carbon\Carbon;
use Propaganistas\LaravelIntl\Facades\Number;
use Illuminate\Support\Facades\App;
?>
@section('content')
    <h3>{{ __('Customers and arrival/departure times') }}</h3>
    <br />
    <br /><br />
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
            @if(sizeof($houses) > 1)
                <tr  style="border-style: solid solid solid solid; border-width:4px 4px 4px 4px; border-color:green;">
                    <form>
                        <th colspan="5">
                            <div class="form-inline">
                                {!! Form::Label('houseid', __('House').':') !!}&nbsp;&nbsp;&nbsp;{!! Form::select('houseid',$houses,$houseid,['class' => 'col-md-3 col form-control', 'onchange' =>  'submit();', 'style' => 'padding: 1px 0 3px 10px;']) !!}
                            </div>
                        </th>
                    </form>
                </tr>
            @endif
            <tr>
                <th>{{ __('Period') }}</th>
                <th>{{__('Arrival') }}</th>
                <th>{{__('Departure') }}</th>
                <th>{{ __('Name')}}</th>
                <th>{{ __('Persons') }}</th>
            </tr>
            @foreach($contractoverview as $model)
                <tr>
                    <td>{{ $model->contract->getPeriodtext(\App::getLocale())}}</td>
                    <td>{{($model->contract->landingdatetime)?$model->contract->landingdatetime->format('Y-m-d h:m'):''}}</td>
                    <td> {{($model->contract->departuredatetime)?$model->contract->departuredatetime->format('Y-m-d h:m'):''}}</td>
                    <td>{{ $model->customer->name }}</td>
                    <td>{{ $model->persons }}</td>
                </tr>
            @endforeach
        </table>
    </div>
@endsection
