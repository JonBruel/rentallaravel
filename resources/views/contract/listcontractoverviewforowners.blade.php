@extends('layouts.app')
<?php
use Carbon\Carbon;
use Propaganistas\LaravelIntl\Facades\Number;
use Illuminate\Support\Facades\App;
?>
@section('content')
    <h3>{{ __('List of leases. Use the selectboxes to filter.') }}</h3>
    <br />
    <div class="table-responsive table-sm">
        <table class="table table-striped">
            <thead>
            <tr  style="border-style: solid solid solid solid; border-width:4px 4px 4px 4px; border-color:green;">
                <form>
                    <th colspan="9">
                        <div class="form-inline">
                            {!! Form::label('yearfrom', __('From').':', ['class' => 'col-md-1']) !!}
                            {!! Form::select('yearfrom',$years,$year,['class' => 'col-md-2 form-control', 'onchange' =>  'submit();', 'style' => 'padding: 1px 0 3px 10px;']) !!}
                            @if(sizeof($houses) > 1)
                                 {!! Form::select('houseid',$houses,$houseid,['class' => 'col-md-3 col form-control', 'onchange' =>  'submit();', 'style' => 'padding: 1px 0 3px 10px;']) !!}
                            @endif
                       </div>

                    </th>
                </form>
            </tr>
            <tr>@if(Gate::allows('Administrator'))
                <th>{{__('Actions') }}</th>
                @endif
                @if(sizeof($houses) > 1)
                    <th>@sortablelink('house.name', __('House'))</th>
                @endif
                <th>@sortablelink('customer.name', __('Name'))</th>
                <th>{{ __('Persons') }}</th>
                <th colspan="2">{{ __('Period') }}</th>
                <th>{{ __('Duration') }}</th>
                @if(Gate::allows('Administrator'))
                    <th>{{ __('Contractamount') }}</th>
                    <th>{{__('Paid') }}</th>
                @endif
            </tr>
            </thead>
            @foreach($contractoverview as $model)
                <tr>
                    @if(Gate::allows('Administrator'))
                        <td>
                            <a href="/contract/contractedit/{{$model->id}}/0" title="{{__('Show contract')}}" data-toggle="tooltip"><span class='glyphicon glyphicon-home'></span></a>
                            <a href="/contract/listaccountposts/{{$model->id}}" title="{{__('Show account')}}" data-toggle="tooltip"><span class='glyphicon glyphicon-euro'></span></a>
                            <a href="/contract/listmails/{{$model->customerid}}" title="{{__('Show mail')}}" data-toggle="tooltip"><span class='glyphicon glyphicon-envelope'></span></a>
                            <a href="/myaccount/editidentitypapers/{{$model->id}}" title="{{__('Passport details')}}" data-toggle="tooltip"><span class='glyphicon glyphicon-eye-open'></span></a>
                        </td>
                    @endif
                    @if(sizeof($houses) > 1)
                        <td>{{$model->house->name}}</td>
                    @endif
                    <td>{{ $model->customer->name }} ({{ $model->id }})</td>
                    <td>{{ $model->persons }}</td>
                    <td colspan="2">{{ $model->from->formatLocalized('%a %d %B %Y')}} - {{ $model->to->formatLocalized('%a %d %B %Y')}}</td>
                    <td>{{ $model->duration }}</td>
                    @if(Gate::allows('Administrator'))
                        <td>{{ Number::format($model->contractamount,['minimum_fraction_digits' => 2, 'maximum_fraction_digits' => 2, 'locale' => App::getLocale()])}}</td>
                        <td>{{ Number::format($model->paid,['minimum_fraction_digits' => 2, 'maximum_fraction_digits' => 2, 'locale' => App::getLocale()])}}</td>
                    @endif
                </tr>
            @endforeach
        </table>
    </div>
@endsection

