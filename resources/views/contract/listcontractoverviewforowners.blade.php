@extends('layouts.app')
<?php
use Carbon\Carbon;
use Propaganistas\LaravelIntl\Facades\Number;
use Illuminate\Support\Facades\App;
?>
@section('content')
    <h3>{{ __('List of leases. Use the selectboxes to filter.') }}</h3>
    <br />
    <br /><br />
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
            <tr>
                <form>
                    <th colspan="9">
                        <div class="form-inline">
                            {!! Form::label('yearfrom', __('From').':', ['class' => 'col-md-1']) !!}
                            {!! Form::select('yearfrom',$years,$year,['class' => 'col-md-2 form-control', 'onchange' =>  'submit();', 'style' => 'padding: 1px 0 3px 10px;']) !!}
                            {!! Form::select('houseid',$houses,$houseid,['class' => 'col-md-3 col form-control', 'onchange' =>  'submit();', 'style' => 'padding: 1px 0 3px 10px;']) !!}
                        </div>

                    </th>
                </form>
            </tr>
            <tr>
                <th>{{__('Actions') }}</th>
                <th>@sortablelink('house.name', __('House'))</th>
                <th>@sortablelink('customer.name', __('Name'))</th>
                <th>{{ __('Persons') }}</th>
                <th colspan="2">{{ __('Period') }}</th>
                <th>{{ __('Duration') }}</th>
                <th>{{ __('Contractamount') }}</th>
                <th>{{__('Paid') }}</th>
            </tr>
            </thead>
            @foreach($contractoverview as $model)
                <tr>
                    <td>
                        <a href="/contract/contractedit/{{$model->id}}/0" title="{{__('Show contract')}}" data-toggle="tooltip"><span class='glyphicon glyphicon-list'></span></a>
                        <a href="/contract/listaccountposts?contractid='.{{$model->id}}" title="{{__('Show account')}}" data-toggle="tooltip"><span class='glyphicon glyphicon-euro'></span></a>
                        <a href="/'customer/listmails?customerid='.{{$model->customerid}}" title="{{__('Show mail')}}" data-toggle="tooltip"><span class='glyphicon glyphicon-envelope'></span></a>
                    </td>
                    <td>{{$model->house->name}}</td>
                    <td>{{ $model->customer->name }}</td>
                    <td>{{ $model->persons }}</td>
                    <td colspan="2">{{ $model->from->formatLocalized('%a %d %B %Y')}} - {{ $model->to->formatLocalized('%a %d %B %Y')}}</td>
                    <td>{{ $model->duration }}</td>
                    <td>{{ Number::format($model->contractamount,['minimum_fraction_digits' => 2, 'maximum_fraction_digits' => 2, 'locale' => App::getLocale()])}}</td>
                    <td>{{ Number::format($model->paid,['minimum_fraction_digits' => 2, 'maximum_fraction_digits' => 2, 'locale' => App::getLocale()])}}</td>
                </tr>
            @endforeach
        </table>
    </div>
@endsection
