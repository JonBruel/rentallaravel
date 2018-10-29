@extends('layouts.app')
<?php
use Carbon\Carbon;
use Propaganistas\LaravelIntl\Facades\Number;
use Illuminate\Support\Facades\App;
?>
@section('content')
    <div class="table-responsive table-sm">
        {!! Form::open(['url' => "contract/commitcontract"]) !!}
            <table class="table table-striped">
                <tr>
                    <th colspan="2" align="center">
                        <div align="center">
                            <img src="/images/step{{ \Session::get('step',2)}}2.gif" width="250" height="50" />
                        </div>
                    </th>
                </tr>
                <tr>
                    <th colspan="2" align="center">
                        {{ __("To accept press 'Accept'") . ' ' . __('You may also choose the currency to be used') }}.
                    </th>
                </tr>
                <tr>
                    <td>
                        {!! Form::label('currencyid', __('Choose currency')) !!}
                    </td>
                    <td>
                        {!! Form::select('currencyid', $currencySelect, $currencyid, ['onChange' => 'setFinalprice()']) !!}
                    </td>
                </tr>
                <tr>
                    <td>
                        {!! Form::label('discount', __('Discount')) !!}
                    </td>
                    <td>
                        {!! Form::text('discount', $contract->discount, ['onChange' => 'setFinalprice()']) !!} %
                    </td>
                </tr>
                <tr>
                    <td>
                        {!! Form::label('finalprice', __('Finalprice')) !!}
                    </td>
                    <td>
                        {!! Form::text('finalprice', $contract->finalprice, ['onChange' => 'setDiscount()']) !!}
                        {!! Form::hidden('price', $contract->price, ['id' => 'hiddenprice']) !!}
                        {!! Form::hidden('id', $contract->id) !!}
                    </td>
                </tr>
                <tr>
                    <td>
                        {!! Form::label('message', __('Message')) !!}
                    </td>
                    <td>
                        {!! Form::textarea('message', $contract->message, ['rows' => 5, 'onChange' => 'newPrice()']) !!}
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        {!! Form::submit(__('Accept'),['class' => "btn btn-success"]); !!} <a href="/contract/chooseweeks?periodid={{$firstperiodid}}&contractid={{$contract->id}}" class="btn btn-warning">{{ __('Back') }}</a>
                    </td>
                </tr>
            </table>
        {!! Form::close() !!}
    </div>
@endsection
@section('scripts')
    <script type="text/javascript">
        /*
        NB: The discount and the finalprice fields are represented using I18N decemal points.
        The other fields use standard american decimal points.
         */

        culture = "{{App::getLocale()}}";
        culture = culture.replace("_", "-");
        Formatter = new Intl.NumberFormat(culture,{ minimumFractionDigits: 2,  maximumFractionDigits: 2});
        decimalseparator = Formatter.format(1.01).substring(1,2);
        thousandsseparator = Formatter.format(1000).substring(1,2);
        blockexecution = false;


        /*
        This function converts a string representation of a number to a number
         */
        function ParseString(string) {
            string = string.replace(thousandsseparator, '');
            string = string.replace(decimalseparator, '.');
            return Number(string);
        }


        //Create rates object as a conversion from PHP
        rates = [];
        @foreach($rates as $cid => $rate)
            rates[{{$cid}}] = {{$rate}};
        @endforeach

        //Initialize oldrate
        oldrate = rates[{{$currencyid}}];

        //Triggered when final price changed
        function setDiscount()
        {
            if (blockexecution) return;
            blockexecution = true;
            discount = Formatter.format(Math.round(10000*($('#hiddenprice').val()-ParseString($('#finalprice').val()))/$('#hiddenprice').val())/100);
            $('#discount').val(discount);
            blockexecution = false;
        }

        //Triggered when discount changed or cuurency changes
        //The price stored is the price in the currency of the house
        function setFinalprice()
        {
            if (blockexecution) return;
            blockexecution = true;
            if ($('#discount').val() == '') $('#discount').val('0');
            discount = ParseString($('#discount').val());

            //The price field value must be changed when the currency is changed, as
            //we want that field to be the non-discounted price in the selected currency.
            rate = rates[$('#currencyid').val()];
            $('#hiddenprice').val(rate*$('#hiddenprice').val()/oldrate);
            oldrate = rate;

            finalprice = $('#hiddenprice').val()*(100-discount)/100;
            $('#finalprice').val(Formatter.format(finalprice));
            blockexecution = false;
        }
    </script>
@endsection
