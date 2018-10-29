@extends('layouts.app')
<?php
use Carbon\Carbon;
use Propaganistas\LaravelIntl\Facades\Number;
use Illuminate\Support\Facades\App;
?>
@section('content')
    <div class="table-responsive table-sm">
        {!! Form::open(['url' => "contract/preparecontract"]) !!}
            <table class="table table-striped">
                <tr>
                    <th colspan="2" align="center">
                        <strong>{{ __('To order follow the two steps!') }}</strong>
                        <p align="center">
                            <img src="/images/step{{ \Session::get('step') }}1.gif" width="250" height="50" />
                        </p>
                    </th>
                </tr>
                <tr>
                    <th colspan="2" align="center">
                        <strong>{{ __('Tick off the weeks you want, enter the number of persons and press "Next".') }}</strong>
                    </th>
                </tr>
                <tr>
                    <th>
                        {!! Form::label('persons', __('Number of persons')) !!}
                    </th>
                    <td>
                        @if($errors->first('persons'))
                            <div class="alert alert-danger row">
                                <div class="alert alert-danger row">
                                    {{ $errors->first('persons') }}
                                </div>
                            </div>
                        @endif
                        {!! Form::select('persons', $personSelectbox, \Session::get('selectedpersons',2), ['onChange' => 'newPrice()','class' => 'col-md-2 form-control', 'style' => 'padding: 1px 0 3px 10px;']) !!}
                    </td>
                </tr>
                <tr>
                    <th>{{ __('Tick!') }}</th>
                    <th>{{ __('Period') }}</th>
                </tr>
                @if(session('weeksnotconsecutive'))
                    <tr>
                        <td colspan="2">
                            <div class="alert alert-danger row">
                                {{ session('weeksnotconsecutive') }}
                            </div>
                        </td>
                    </tr>
                @endif
                @foreach($periodcontracts as $key => $periodcontract)
                <tr style="{{ ($periodcontract->committed > 0)?'background-color: #FF9191;':'' }}">
                    <td>
                        @if(!($periodcontract->committed > 0))
                            {!! Form::checkbox('checkedWeeks['.$periodcontract->id.']', $periodcontract->id, ($checkedWeeks[$periodcontract->id] == 1),['onClick' => 'newPrice()']); !!}
                        @else
                            {{ __('Occupied') }}
                        @endif
                        {!! Form::hidden('ids['.$periodcontract->id.']', $periodcontract->id) !!}
                    <td>{{ $periodcontract->getEnddays(App::getLocale()) }}</td>
                </tr>
                @endforeach
                <tr>
                    <td colspan="1">
                         {!! Form::submit(__('Next'),['class' => "btn btn-success"]); !!}
                    </td>
                    <th>
                        {!! __('Price in your preferred currency:') . ' ' . $periodcontract->getRate(App::getLocale())['currencysymbol'] . '&nbsp;&nbsp;<span id="price"></span>' !!}
                        {!! Form::hidden('houseid', $periodcontract->houseid) !!}
                        {!! Form::hidden('contractid', $contractid) !!}
                    </th>
                </tr>
            </table>
        {!! Form::close() !!}
    </div>
@endsection
@section('scripts')

    <script type="text/javascript">

        baseprice = [];
        personprice = [];
        basepersons = [];
        rate = {{ $periodcontract->getRate(App::getLocale())['rate'] }}


        //Define the price variables
        @foreach ($periodcontracts as $key => $periodcontract)
            baseprice[{{$periodcontract->id}}] = {{$periodcontract->baseprice}};
            personprice[{{$periodcontract->id}}] = {{$periodcontract->personprice}};
            basepersons[{{$periodcontract->id}}] = {{$periodcontract->basepersons}};
        @endforeach

        function newPrice()
        {
            price = 0;
            culture = "{{App::getLocale()}}";
            culture = culture.replace("_", "-");
            //Get chosen number of persons
            persons = $('#persons').val();

            //Calculate the price in the house currency
            $("input:checked").each(function() {price += baseprice[this.value] + (persons - basepersons[this.value])*personprice[this.value]});

            //Show the price in customer currency
            $('#price').html(new Intl.NumberFormat(culture,{ minimumFractionDigits: 2,  maximumFractionDigits: 2}).format(price*rate));
        }
        newPrice();
    </script>

@endsection
