@extends('layouts.app')
@section('content')
    <h3>{{__('Account posts')}}</h3>
    <br /><br />
    <div class="table-responsive">

        <table class="table table-striped">
        <tr>
            <td>
                <strong>{{ __('Date') }}</strong>
            </td>
            <td>
                <strong>{{ __('Text') }}</strong>
            </td>
            <td>
                <strong>{{ __('Amount') }} {{$currencysymbol}}</strong>
            </td>
        </tr>
        <?php App\Models\Accountpost::$ajax=false;?>
        @foreach($models as $model)
            <tr>
                <td>
                    {{ $model->created_at->format('Y-m-d') }}
                </td>
                <td>
                    {{__($model->posttype->posttype)}}
                </td>
                <td id="{{$model->id}}">
                    @if(substr($model->amount,0,1) == '-')
                        {{substr($model->amount,1,10)}}
                    @else
                    -{{$model->amount}}
                    @endif
                </td>
            </tr>
        @endforeach
        <tr>
            <td>
                {{ __('Payment') }}
            </td>
            <td>
                <strong>Selectbox</strong>
            </td>
            <td>
                <strong id="suggested"></strong>
            </td>
        </tr>
        <tr>
            <td>
            </td>
            <td>
                <strong>{{ __('Total') }}</strong>
            </td>
            <td>
                <strong id="total"></strong>
            </td>
        </tr>
        </table>
    </div>
@endsection
@section('scripts')
    <script type="text/javascript">
        culture = "{{App::getLocale()}}";
        culture = culture.replace("_", "-");
        Formatter = new Intl.NumberFormat(culture,{ minimumFractionDigits: 2,  maximumFractionDigits: 2});
        decimalseparator = Formatter.format(1.01).substring(1,2);
        thousandsseparator = Formatter.format(1000).substring(1,2);
        total = 0;

        /*
        This function converts a string representation of a number to a number
         */
        function ParseString(string) {
            string = string.replace(thousandsseparator, '');
            string = string.replace(decimalseparator, '.');
            return Number(string);
        }

        @foreach($models as $model)
                total += ParseString('{{$model->amount}}');
        @endforeach


        $(document).ready(showBalance());


        //Triggered when discount changed or cuurency changes
        //The price stored is the price in the currency of the house
        function showBalance()
        {
            $('#total').html(Formatter.format(-total));
            $('#suggested').html(Formatter.format(total));
        }

        //Triggered when final price changed
        function setDiscount()
        {
        }

        function newPrice()
        {
        }

    </script>
@endsection