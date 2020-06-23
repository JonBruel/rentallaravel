@extends('layouts.app')
<?php
use Carbon\Carbon;
use Propaganistas\LaravelIntl\Facades\Number;
use Illuminate\Support\Facades\App;
?>
@section('content')
    <h3>{{ __('Commercial house usage over the entire year') }}</h3>
    <br />
    @php
        $contractamount = 0;
        $paid = 0;
    @endphp
    <div class="table-responsive table-sm">
        <table class="table table-striped">
            <thead>
            <tr  style="border-style: solid solid solid solid; border-width:4px 4px 4px 4px; border-color:green;">
                <form>
                    <th colspan="3">
                        {!! Form::select('year',$years,$year,['class' => 'col-md-12 col form-control', 'onchange' =>  'submit();', 'style' => 'padding: 1px 0 3px 10px;']) !!}
                    </th>
                    @if(sizeof($houses) > 1)
                    <th colspan="6">
                        {!! Form::select('houseid',$houses,$houseid,['class' => 'col-md-5 col form-control', 'onchange' =>  'submit();', 'style' => 'padding: 1px 0 3px 10px;']) !!}
                    </th>
                    @else
                        <th colspan="6">
                        </th>
                    @endif

                </form>
            </tr>
            <tr>@if(sizeof($houses) > 1)
                <th>@sortablelink('house.name', __('House'))</th>
                @endif
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
                    @if(sizeof($houses) > 1)
                    <td>
                        <a href="/home/listhouses?defaultHouse={{$model->id}}">{{$model->house->name}}</a>
                    </td>
                    @endif
                    <td>{{ $model->customer->name }}</td>
                    <td>{{ $model->persons }}</td>
                    <td colspan="2">{{ $model->from->formatLocalized('%a %d %B %Y')}} - {{ $model->to->formatLocalized('%a %d %B %Y')}}</td>
                    <td>{{ $model->duration }}</td>
                    <td>{{ $model->from->quarter }}</td>
                    <td>{{ Number::format($model->contractamount*$model->contract->getExchangeRate(),['minimum_fraction_digits' => 2, 'maximum_fraction_digits' => 2, 'locale' => App::getLocale()])}}@php($contractamount += $model->contractamount*$model->contract->getExchangeRate())</td>
                    <td>{{ Number::format($model->paid*$model->contract->getExchangeRate(),['minimum_fraction_digits' => 2, 'maximum_fraction_digits' => 2, 'locale' => App::getLocale()])}}@php($paid += $model->paid*$model->contract->getExchangeRate())</td>
                </tr>
            @endforeach
            <tr>
                @if(sizeof($houses) > 1)
                <td>
                </td>
                @endif
                <td colspan="5"></td>
                <td>{{ __('Totals') }}:</td>
                <td><strong id="contractamount">{{ $contractamount }}</strong></td>
                <td><strong id="paid">{{ $paid }}</strong></td>
            </tr>
        </table>
    </div>
@endsection
@section('scripts')
    <link href="{{ asset('/vendor/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css', config('app.secure', false))}}" rel="stylesheet">
    <script type="text/javascript" src="{{ asset('/vendor/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js', config('app.secure', false))}}"></script>
    <script type="text/javascript">
        culture = "{{App::getLocale()}}";
        culture = culture.replace("_", "-");
        Formatter = new Intl.NumberFormat(culture,{ minimumFractionDigits: 2,  maximumFractionDigits: 2});
        decimalseparator = Formatter.format(1.01).substring(1,2);
        thousandsseparator = Formatter.format(1000).substring(1,2);

        /*
        This function converts a string representation of a number to a number
         */
        function ParseString(string) {
            string = string.replace(thousandsseparator, '');
            string = string.replace(decimalseparator, '.');
            return Number(string);
        }

        $('#paid').text(Formatter.format($('#paid').text()));
        $('#contractamount').text(Formatter.format($('#contractamount').text()));



    </script>
@endsection
