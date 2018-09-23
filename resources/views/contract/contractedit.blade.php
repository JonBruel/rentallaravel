@extends('layouts.app')
@section('content')
    <h4>{{$models[0]->house->name}}, {{$models[0]->house->address1}}, {{$models[0]->house->country}}</h4>
    <h4>{{$models[0]->customer->name}}, {{__('Contract').': '.$models[0]->id}}</h4>
    <div class="table">
        {!! Form::model($models[0], ['action' => ['ContractController@contractupdate', $models[0]], 'class' => 'form-horizontal']) !!}
        <br />
        <br />
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @foreach($fields as $field)
            <div class="text-danger col-8 field-validation-valid row" data-valmsg-for="{{$field}}" data-valmsg-replace="true"></div>
            @if($errors->first($field))
                <div class="alert alert-danger row">
                    {{ $errors->first($field) }}
                </div>
            @endif
            <div class="form-group row">
                @if(($field != 'currencyid') && (($field != 'discount') || (!$fromcalendar)))
                    {!! Form::label($field, __(ucfirst($field)).':', ['class' => 'control-label col-3 col']) !!}
                @endif
                    @if(str_contains($field, 'time'))
                        {!! Form::text($field, $models[0]->$field, $vattr->validationOptions($field, ['class' => 'col-5 col form-control'])) !!}
                    @endif
                    @if($field == 'discount')
                        @if(!$fromcalendar)
                            {!! Form::text($field, $models[0]->$field, $vattr->validationOptions($field, ['class' => 'col-2 col form-control clearfix', 'onChange' => 'setFinalprice()'])) !!}
                        @else
                            {!! Form::hidden($field, 0, ['id' => 'discount']) !!}
                         @endif
                     @endif
                     @if($field == 'finalprice')
                         {!! Form::text($field, $models[0]->$field, $vattr->validationOptions($field, ['class' => 'col-3 col form-control', 'onChange' => 'setDiscount()', 'style' => 'height: 35px;'])) !!}
                        @if(sizeof($currencySelect) > 0)
                            {!! Form::select('currencyid', $currencySelect, $models[0]->currencyid, ['class' => 'col-2 form-control', 'onChange' => 'setFinalprice()', 'id' => 'currencyid', 'style' => 'padding: 1px 0 3px 10px; height:35px']) !!}
                        @endif
                    @endif
                    @if($field == 'persons')
                            {!! Form::select($field, $personSelectbox, $models[0]->$field, $vattr->validationOptions($field, ['class' => 'col-2 form-control', 'onChange' => 'setFinalprice()', 'style' => 'padding: 1px 0 3px 10px;'])) !!}
                    @endif
                    @if($field == 'message')
                        {!! Form::textarea($field, $models[0]->$field, $vattr->validationOptions($field, ['class' => 'col-8 col form-control clearfix'])) !!}
                    @endif
            </div>
        @endforeach
        {!! Form::hidden('fromcalendar', ($fromcalendar)?1:0) !!}
        {!! Form::hidden('periodid', $periodid) !!}
        {!! Form::hidden('id', $models[0]->id) !!}
        {!! Form::hidden('price', $models[0]->price, ['id' => 'hiddenprice']) !!}
        <div class="border border-primary rounded">

            <div>
                <button class='btn btn-success' onclick='getWeeks(offchange(-1));return false'>{{__('More')}}</button>
                <button class='btn btn-success' onclick='getWeeks(lessWeeks());return false'>{{__('Less')}}</button>
            </div>

            <div id="showweeks" class="row col-12" style="margin-left: 0">
                <h3>{{__('Please wait until the weeks are shown!')}}</h3>
            </div>

            <div>
                <button class='btn btn-success' onclick='getWeeks(offchange(1));return false;'>{{__('More')}}</button>
                <button class='btn btn-success' onclick='getWeeks(lessWeeks());return false'>{{__('Less')}}</button>
            </div>
        </div>
        <br />
        {!! Form::submit('Save changes',['class' => "btn btn-success"]); !!}
        {!! Form::close() !!}
        <br />
        @if(!$fromcalendar)
            {!! $models->appends(\Request::except('page'))->render() !!}
        @endif
    </div>
    @include('partials.client_validation')
@endsection
@section('scripts')
    <link href="{{ asset('/vendor/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css', true)}}" rel="stylesheet">
    <script type="text/javascript" src="{{ asset('/vendor/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js', true)}}"></script>
    <script type="text/javascript">
        culture = "{{App::getLocale()}}";
        culture = culture.replace("_", "-");
        Formatter = new Intl.NumberFormat(culture,{ minimumFractionDigits: 2,  maximumFractionDigits: 2});
        decimalseparator = Formatter.format(1.01).substring(1,2);
        thousandsseparator = Formatter.format(1000).substring(1,2);
        blockexecution = false;
        rate = ParseString($('#finalprice').val())/$('#hiddenprice').val();
        rates = [];
        @if(sizeof($currencySelect) > 0)
            @foreach($rates as $cid => $rate)
                rates[{{$cid}}] = {{$rate}};
            @endforeach
        @endif

        /*
        This function converts a string representation of a number to a number
         */
        function ParseString(string) {
            string = string.replace(thousandsseparator, '');
            string = string.replace(decimalseparator, '.');
            return Number(string);
        }



        offserminus = 0;
        offsetplus = 0;
        periodchunk = [];
        baseprice = [];
        personprice = [];
        basepersons = [];
        function offchange(changeoffset)
        {
            if (changeoffset > 0) {
                offsetplus++;
                return offsetplus;
            }
            if (changeoffset < 0) {
                offserminus--;
                return offserminus;
            }
            if (changeoffset == 0) {
                return 0;
            }
        }

        $(document).ready(getWeeks(0));
        $(function() {
            $('#landingdatetime').datetimepicker({ language: '{{str_replace('_', '-', App::getLocale())}}',
                format: 'yyyy-mm-dd hh:ii',
                minuteStep: 30,
                autoclose: true,
                startView: 1});

            $('#departuredatetime').datetimepicker({ language: '{{str_replace('_', '-', App::getLocale())}}',
                format: 'yyyy-mm-dd hh:ii',
                minuteStep: 30,
                autoclose: true,
                startView: 1});
        });



        /*Build ajax call to populate showweeks division
          We load the week in chuncks and save the chuncks in the
          global periodchunk array.
         */
        function getWeeks(offset)
        {
            culture = '{{App::getLocale()}}';
            contractid = {{$models[0]->id}};
            periodid = 0;
            houseid = {{$models[0]->houseid}};
            url = '/ajax/getweeks/' + houseid + '/' + culture + '/' + offset + '/' + periodid + '/' + contractid ;
            new $.getJSON(url, function(periods)
            {
                content = '<table class="table table-striped"><tr><th>{{__('Tick period')}}</th><th>{{__('Period')}}</th></tr>';
                //We add a large number to ensure the index is positive
                periodchunk[offset+1000] = periods;
                periodchunk.forEach(periods => {
                    periods.forEach(period => {
                        rate = period.rate;
                        baseprice[period.id] = period.baseprice;
                        personprice[period.id] = period.personprice;
                        basepersons[period.id] = period.basepersons;
                        checked = '';
                        style = '';
                        if (period.chosen) checked = ' checked = "checked"';
                        free = '<input onClick="setFinalprice()"' + checked + ' name="checkedWeeks[' + period.id + ']" type="checkbox" value="'+period.id+'">';
                        if (period.committed && !period.chosen) {
                            free = '{{__('Occupied')}}';
                            style = ' style = "background-color: #FF9191;"';
                        }
                        content += '<tr' + style + '><td>' + free + '</td><td>' + period.periodtext + '</td></tr>';
                    });
                });
                content += '</tr>';

                $('#showweeks').html(content);
                if ($('#hiddenprice').val() == 0) setFinalprice();
            });
        }

        function lessWeeks()
        {
            offserminus = 0;
            offsetplus = 0;
            periodchunk = [];
            getWeeks(0);
        }

        //Triggered when discount changed or cuurency changes
        //The price stored is the price in the currency of the house
        function setFinalprice()
        {
            if (blockexecution) return;
            blockexecution = true;
            if ($('#discount').val() == '') $('#discount').val('0');
            discount = ParseString($('#discount').val());
            newPrice();
            finalprice = $('#hiddenprice').val()*(100-discount)/100;
            $('#finalprice').val(Formatter.format(finalprice));
            blockexecution = false;
        }

        //Triggered when final price changed
        function setDiscount()
        {
            if (blockexecution) return;
            blockexecution = true;
            discount = Formatter.format(Math.round(10000*($('#hiddenprice').val()-ParseString($('#finalprice').val()))/$('#hiddenprice').val())/100);
            $('#discount').val(discount);
            blockexecution = false;
        }

        function newPrice()
        {
            price = 0;
            //Get chosen number of persons
            persons = $('#persons').val();

            //Calculate the price in the house currency
            $("input:checked").each(function() {price += baseprice[this.value] + (persons - basepersons[this.value])*personprice[this.value]});

            @if(sizeof($currencySelect) > 0)
                rate = rates[$('#currencyid').val()];
                console.log('Currencyid is: '+$('#currencyid').val());
            @endif

            $('#hiddenprice').val(rate*price);
            return rate*price;
        }

    </script>
@endsection