@extends('layouts.app')
<?php
use Carbon\Carbon;
use Propaganistas\LaravelIntl\Facades\Number;
use Illuminate\Support\Facades\App;
?>
@section('content')
    <h3>{{ __('Commercial house usage over the entire year') }}</h3>
    <br />
    <br /><br />
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
            <tr>
                <form>
                    <th colspan="3">
                        {!! Form::select('year',$years,$year,['class' => 'col-md-12 col form-control', 'onchange' =>  'submit();', 'style' => 'padding: 1px 0 3px 10px;']) !!}
                    </th>
                    <th colspan="6">
                        {!! Form::select('houseid',$houses,$houseid,['class' => 'col-md-5 col form-control', 'onchange' =>  'submit();', 'style' => 'padding: 1px 0 3px 10px;']) !!}
                    </th>
                </form>
            </tr>
            <tr>
                <th>@sortablelink('house.name', __('House'))</th>
                <th>@sortablelink('customer.name', __('Name'))</th>
                <th>{{ __('Persons') }}</th>
                <th colspan="2">{{ __('Period') }}</th>
                <th>{{ __('Duration') }}</th>
                <th>{{ __('Quarter') }}</th>
                <th>{{ __('Contractamount') }}</th>
                <th>{{__('Paid') }}</th>
            </tr>
            </thead>
            @foreach($contractoverview as $model)
                <tr>
                    <td>
                        <a href="/home/listhouses?defaultHouse={{$model->id}}">{{$model->house->name}}</a>
                    </td>
                    <td>{{ $model->customer->name }}</td>
                    <td>{{ $model->persons }}</td>
                    <td colspan="2">{{ $model->from->formatLocalized('%a %d %B %Y')}} - {{ $model->to->formatLocalized('%a %d %B %Y')}}</td>
                    <td>{{ $model->duration }}</td>
                    <td>{{ $model->from->quarter }}</td>
                    <td>{{ Number::format($model->contractamount,['minimum_fraction_digits' => 2, 'maximum_fraction_digits' => 2, 'locale' => App::getLocale()])}}</td>
                    <td>{{ Number::format($model->paid,['minimum_fraction_digits' => 2, 'maximum_fraction_digits' => 2, 'locale' => App::getLocale()])}}</td>
                </tr>
            @endforeach
        </table>
    </div>
@endsection
