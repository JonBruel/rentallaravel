@extends('layouts.app')
@section('content')
    <h3>{{__('Account posts')}}</h3>
    <br /><br />
    <div class="table-responsive col-md-12 table-sm">
        <table class="table table-striped">
            <tr>
                @if(Gate::allows('Supervisor'))
                    <td>
                        <strong>{{ __('Actions') }}</strong>
                    </td>
                @endif
                <td>
                    <strong>{{ __('Date') }}</strong>
                </td>
                <td>
                    <strong>{{ __('Post type') }}</strong>
                </td>
                <td>
                    <strong>{{ __('Post comment') }}</strong>
                </td>
                <td>
                    <strong>{{ __('Amount') }} {{$currencysymbol}}</strong>
                </td>
            </tr>
            <?php App\Models\Accountpost::$ajax=false;?>
            @if(sizeof($models) > 0)
            @foreach($models as $model)
                <tr>
                    @if(Gate::allows('Supervisor'))
                        <td>
                            @include('partials.edit_delete', ['path' => 'accountpost', 'id' => $model->id, 'deleteallowed' => Gate::allows('Supervisor')])
                        </td>
                    @endif
                    <td>
                        {{ $model->created_at->format('d-m-Y') }}
                    </td>
                    <td>
                        {{__($model->text)}}
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
                <td colspan="{{(Gate::allows('Supervisor'))?3:2}}">
                </td>
                <td>
                    <strong>{{ __('Total') }}</strong>
                </td>
                <td>
                    <strong id="total"></strong>
                </td>
            </tr>
            {!! Form::open(['url' => '/contract/registerpayment/'.$models[0]->contractid]) !!}
                <tr style="border-style: solid solid none solid; border-width:4px 4px 0px 4px; border-color:red;">
                    <td colspan="{{(Gate::allows('Supervisor'))?2:1}}">
                        {!! Form::label('text', __('Text').':') !!}
                    </td>
                    <td>
                        {!! Form::text('text', __('Registration of bank account post'), ['class' => 'form-control col-md-11', 'style' => "height: 28px", 'id' => 'text']) !!}
                    </td>
                    <td>
                        {!! Form::label('amount', __('Amount').':') !!}

                    </td>
                    <td>
                        {!! Form::text('amount', '', ['dusk' => 'amount', 'class' => 'form-control col-md-11', 'style' => "height: 28px", 'id' => 'suggested']) !!}
                    </td>
                </tr>
            <tr style="border-style: none solid none solid; border-width:4px; border-color:red;">
                <td colspan="{{(Gate::allows('Supervisor'))?2:1}}">
                    {!! Form::label('posttypeid', __('Posttype').':') !!}
                </td>
                <td>
                    {!! Form::select('posttypeid',[50 => __('Prepayment'), 100 => __('Final payment received'), 300 => __('Rounding and currency adjustments'), 5 => __('For test mails')],'',['dusk' => 'posttypeid', 'class' => 'form-control col-md-11', 'style' => 'padding: 1px 0 3px 10px;']) !!}
                </td>
                <td>
                    {!! Form::label('round', __('Tick if you want to make balance zero').':') !!}
                </td>
                <td>
                    {!! Form::checkbox('round', 1, false, ['class' => 'form-control col-md-1']) !!}
                </td>
            </tr>
                <tr style="border-style:none solid solid solid; border-width:4px; border-color:red;">
                    <td colspan="{{(Gate::allows('Supervisor'))?4:3}}">

                    </td>

                    <td>
                        {!! Form::hidden('contractid', $models[0]->contractid) !!}
                        {!! Form::submit(__('Save payment registration'),['dusk' => 'next', 'class' => "btn btn-primary"]); !!}
                    </td>
                </tr>
            {!! Form::close() !!}
            @else
                <tr><td colspan="{{(Gate::allows('Supervisor'))?5:4}}">{{__('No account posts found.')}}</td></tr>
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
            $('#suggested').val(Formatter.format(total));
        }

    </script>
@endsection
