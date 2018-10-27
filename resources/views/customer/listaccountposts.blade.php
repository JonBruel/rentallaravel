@extends('layouts.app')
@section('content')
    <h3>{{__('Account posts for ')}}{{$customername}}</h3>
    <div class="alert alert-warning border border-primary">{{__('Order: latest first') }}.</div>
    <br /><br />
    <div class="table-responsive col-md-12">

        <table class="table table-striped">
            <?php
                $header = "
            <tr>
                <td>
                    <strong>". __('Actions')."</strong>
                </td>
                <td>
                    <strong>". __('Date') ."</strong>
                </td>
                <td>
                    <strong>". __('Text') ."</strong>
                </td>
                <td>
                    <strong>". __('Type') ."</strong>
                </td>
                <td>
                    <strong>". __('Passified by') ."</strong>
                </td>
                 <td>
                    <strong>". __('Amount') ."</strong>
                </td>
            </tr>";
            ?>
            {!! $header !!}
            <tr>
                <th colspan="6">
                    <strong>
                        {{__('Account posts not related to a specific contract.')}}
                    </strong>
                </th>
            </tr>
            @php($startcontractnumber = 0)
            <?php
                App\Models\Accountpost::$ajax=false;
            ?>
            @if(sizeof($models) > 0)
            @foreach($models as $model)
            <?php
                $headline = false;
                if($startcontractnumber != $model->contractid)
                {
                    $startcontractnumber = $model->contractid;
                    $headline = true;
                }
                if(!isset($subtotalid)) $subtotalid = $model->id;
            ?>
            @if($headline)
                    <tr>
                        <th colspan="4">
                            <strong>
                            </strong>
                        </th>
                        <th>
                            <strong>
                                {{__('Sub total')}}
                            </strong>
                        </th>
                        <th>
                            <strong id="subtotal{{$subtotalid}}"></strong>
                        </th>
                    </tr>
                <tr>
                    <th colspan="6">
                        <strong>
                            {{__('Contract number')}}: {{ $model->contractid }}
                        </strong>
                    </th>
                </tr>
                {!! $header !!}
            @endif

                <tr>
                    <td>
                        @include('partials.edit_delete', ['path' => 'accountpost', 'id' => $model->id, 'deleteallowed' => Gate::allows('Supervisor')])
                    </td>
                    <td>
                        {{ $model->created_at->format('Y-m-d') }}
                    </td>
                    <td>
                        {{__($model->text)}}
                    </td>
                    <td>
                        {{__($model->posttype->posttype)}}
                    </td>
                    <td>
                        {{ ($model->passifiedby != 0)?$model->passifiedby:''}}
                    </td>
                    <td id="{{$model->id}}">
                        @if(substr($model->amount,0,1) == '-')
                            {{substr($model->amount,1,10)}}
                        @else
                        -{{$model->amount}}
                        @endif
                    </td>
                </tr>
                @php($subtotalid = $model->contractid)
            @endforeach
            <tr>
                <th colspan="4">
                    <strong>
                    </strong>
                </th>
                <th>
                    <strong>
                        {{__('Sub total')}}
                    </strong>
                </th>
                <th>
                    <strong id="subtotal{{$subtotalid}}"></strong>
                </th>
            </tr>
            <tr>
                <td colspan="4">
                </td>
                <td>
                    <strong>{{ __('Total') }}</strong>
                </td>
                <td>
                    <strong id="total"></strong>
                </td>
            </tr>
            @else
                    <tr>
                        <td colspan="6">
                            {{__('No account posts found.')}}
                        </td>
                    </tr>
            @endif
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
        subtotal = 0;
        oldid = 0;

        /*
        This function converts a string representation of a number to a number
         */
        function ParseString(string) {
            string = string.replace(thousandsseparator, '');
            string = string.replace(decimalseparator, '.');
            return Number(string);
        }

        @foreach($models as $model)

                newid = {{$model->contractid}};
                if (newid != oldid)
                {
                    $('#subtotal'+oldid).html(Formatter.format(-subtotal));
                    oldid = newid;
                    subtotal = 0;
                }
        total += ParseString('{{$model->amount}}');
        subtotal += ParseString('{{$model->amount}}');
        @endforeach

        $('#subtotal'+newid).html(Formatter.format(-subtotal));
        $(document).ready(showBalance());


        //Triggered when discount changed or cuurency changes
        //The price stored is the price in the currency of the house
        function showBalance()
        {
            $('#total').html(Formatter.format(-total));
            $('#suggested').val(Formatter.format(total));
        }

    </script>
@endsection