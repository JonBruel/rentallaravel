@extends('layouts.app')
@section('content')
    <div class="modal" tabindex="-1" role="dialog" id="adjustguestsinfo">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Tip') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>{{ __('You may adjust the number of guests for each period chosen.') }}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


    <h4 style="text-align: center">{{$models[0]->house->name}}</h4>
    @if(Gate::allows('Administrator'))<h4>{{$models[0]->customer->name}}, {{__('Contract').': '.$models[0]->id}}</h4>@endif
    <div class="table">
        {!! Form::open(['action' => ['ContractController@contractupdate', $models[0]], 'class' => 'form-horizontal', 'autocomplete' => 'off']) !!}
        @if(Gate::allows('Administrator'))
            {!! Form::submit(__('Save changes'),['class' => "btn btn-primary col col-md-2", 'name' => 'Book', 'style' => 'opacity: 0.1']); !!}
        @else
            {!! Form::submit(__('Book house'),['class' => "btn btn-primary col col-md-12", 'name' => 'Book', 'style' => 'opacity: 0.1']); !!}
        @endif
        @if(Gate::allows('Owner'))
            {!! Form::submit(__('Delete booking'),['class' => "btn btn-primary", 'name' => 'Delete']); !!}
        @endif
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
        @if($models[0]->status == "Uncommitted")
            <div class="form-group row">
                <strong> {{__("Contract has been cancelled because of non-payment")}} </strong>
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
                @if(($field != 'currencyid') && ($field != 'categoryid') && (($field != 'discount') || (!$fromcalendar) || Gate::allows('Administrator')))
                    {!! Form::label($field, __(ucfirst($field)).':', ['class' => 'control-label col-3 col']) !!}
                @endif
                @if($field == 'categoryid')
                    @if(Gate::allows('Administrator'))
                        {!! Form::label($field, __(ucfirst($field)).':', ['class' => 'control-label col-3 col']) !!}
                        {!! Form::select($field, $models[0]->withSelect($field), $models[0]->categoryid, ['class' => 'col-2 form-control', 'onChange' => 'setFinalprice()', 'id' => 'currencyid', 'style' => 'padding: 1px 0 3px 10px; height:35px']) !!}
                    @else
                        {!! Form::hidden($field, $models[0]->$field, ['id' => $field]) !!}
                    @endif
                @endif
                @if(str_contains($field, 'time'))
                    {!! Form::text($field, $models[0]->$field, $vattr->validationOptions($field, ['class' => 'col-5 col form-control'])) !!}
                @endif
                @if($field == 'discount')
                    @if((!$fromcalendar) || (Gate::allows('Administrator')))
                        {!! Form::text($field, $models[0]->$field, $vattr->validationOptions($field, ['class' => 'col-2 col form-control clearfix', 'onChange' => 'setFinalprice()'])) !!}
                    @else
                        {!! Form::hidden($field, 0, ['id' => 'discount']) !!}
                    @endif
                 @endif
                 @if($field == 'finalprice')
                    @if(Gate::allows('Administrator'))
                        {!! Form::text($field, $models[0]->$field, $vattr->validationOptions($field, ['class' => 'col-4 col form-control', 'onChange' => 'setDiscount()', 'style' => 'height: 35px;'])) !!}
                    @else
                        {!! Form::text($field, $models[0]->$field, $vattr->validationOptions($field, ['class' => 'col-4 col form-control', 'readonly' => true, 'style' => 'font-weight: bold; height: 35px;'])) !!}
                    @endif
                    @if(sizeof($currencySelect) > 0)
                        {!! Form::select('currencyid', $currencySelect, $models[0]->currencyid, ['class' => 'col-4 form-control', 'onChange' => 'setFinalprice()', 'id' => 'currencyid', 'style' => 'padding: 1px 0 3px 10px; height:35px']) !!}
                    @endif
                @endif
                @if($field == 'persons')
                    {!! Form::hidden($field, $models[0]->$field, $vattr->validationOptions($field, ['autocomplete' => 'off', 'class' => 'col-2 form-control', 'onChange' => 'setFinalprice()', 'style' => 'padding: 1px 0 3px 10px;', 'id' => 'persons'])) !!}
                    <span id="persons_0">{{$models[0]->$field}}</span>
                    <button class="glyphicon glyphicon-plus rounded-circle" style="margin-left: 10px" onclick="addPersons(1);return false;"></button>
                    <button class="glyphicon glyphicon-minus rounded-circle"  style="margin-left: 10px" onclick="addPersons(-1);return false;"></button>
                @endif
                @if($field == 'message')
                    {!! Form::textarea($field, $models[0]->$field, $vattr->validationOptions($field, ['class' => 'col-9 col form-control clearfix', 'style' => 'max-width: 73%'])) !!}
                @endif
            </div>
        @endforeach
        {!! Form::hidden('fromcalendar', ($fromcalendar)?1:0) !!}
        {!! Form::hidden('ratechosen', 0, ['id' => 'ratechosen']) !!}
        {!! Form::hidden('periodid', $periodid) !!}
        {!! Form::hidden('id', $models[0]->id, ['dusk' => 'id']) !!}
        {!! Form::hidden('price', $models[0]->price, ['id' => 'hiddenprice']) !!}

        <div class="border border-primary rounded col col-md-12" style="border-width: 3px">
            <div style="text-align: right;">
                <!--<button class='btn btn-success' onclick='getWeeks(lessWeeks(0));return false'>{{__('Reset')}}</button>-->
            </div>
            <div class="modal" id="notAdjecent">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close glyphicon glyphicon-remove" data-dismiss="modal" aria-hidden="Close"></button>
                        </div>
                        <div style="margin: 10px">
                            <h4>{{ __('The periods chosen must be adjacent without other periods in between').'.' }}</h4>
                        </div>

                        <div class="modal-footer">
                            <button type="button" type="button" data-dismiss="modal" class="btn btn-primary">{{ __('Close') }}</button>
                        </div>
                    </div>
                </div>
            </div>
            <div style="max-height: 290px; overflow-y: scroll; margin-top: 3px; margin-bottom: 3px" id="calendar">
                <div id="showweeks" class="row col-12" style="margin-left: 0">
                    <h3>{{__('Please wait until the weeks are shown!')}}</h3>
                    <div style="background: rgba( 255, 255, 255, .8 ) url('/images/ajax-loader.gif') 50% 50%  no-repeat;"></div>
                </div>
            </div>
        </div>
        <br />
        @if(Gate::allows('Administrator'))
            {!! Form::submit(__('Save changes'),['class' => "btn btn-primary col col-md-2", 'name' => 'Book', 'dusk' => 'next', 'style' => 'opacity: 0.1']); !!}
        @else
            {!! Form::submit(__('Book house'),['class' => "btn btn-primary col col-md-12", 'name' => 'Book', 'dusk' => 'next', 'style' => 'opacity: 0.1']); !!}
         @endif
        @if(Gate::allows('Owner'))
            {!! Form::submit(__('Delete booking'),['class' => "btn btn-primary", 'name' => 'Delete']); !!}
        @endif
        {!! Form::close() !!}
        <br />
        @if(!$fromcalendar)
            {!! $models->appends(\Request::except('page'))->render() !!}
        @endif

    </div>

    @include('partials.client_validation')
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



        offsetminus = 0;
        offsetplus = 0;
        showadjustguestsinfo = false;
        adjustOffsetMinus = true;
        periodchunk = [];
        baseprice = [];
        personprice = [];
        basepersons = [];
        checkedstatus = [];
        from = [];
        to = [];
        basepersonscommon = 2;
        maxpersonscommon = 8;
        function offchange(changeoffset)
        {
            if (changeoffset > 0) {
                offsetplus++;
                return offsetplus;
            }
            if (changeoffset < 0) {
                if (adjustOffsetMinus) offsetminus--;
                return offsetminus;
            }
            if (changeoffset == 0) {
                return 0;
            }
        }


        $(document).ready(function(){
            $("[name='Book']").css('opacity', 0.2);
            getWeeks(0);
            addPersons(0);
        });

        $("#calendar").scrollTop(20);
        usedheight = $("#calendar").height();
        cont = true;
        $('#calendar').scroll(function() {
            if (cont)
            {

                percentage = 100*$( "#calendar" ).scrollTop()/usedheight;
                console.log('percentage: '+percentage);
                console.log('usedheight: '+usedheight);
                if (percentage > 85)
                {
                    cont = false;
                    usedheight = Math.max($("#showweeks").height(), $("#calendar").height());
                    setTimeout(function(){cont = true; }, 2000);
                    console.log("Scroll relative position: " + percentage + "\n actual position " + $("#calendar").scrollTop() +  "\n usedheight height: " + usedheight + "\n weeks height: " + $("#showweeks").height() + "." );
                    getWeeks(offchange(1));
                }
                if (percentage == 0)
                {
                    if (adjustOffsetMinus)
                    {
                        cont = false;
                        usedheight = Math.max($("#showweeks").height(), $("#calendar").height());
                        setTimeout(function(){cont = true; }, 2000);
                        console.log("Scroll relative position: " + percentage + "\n actual position " + $("#calendar").scrollTop() +  "\n usedheight height: " + usedheight + "\n weeks height: " + $("#showweeks").height() + "." );
                        getWeeks(offchange(-1));
                        $("#calendar").scrollTop(10);
                    }
                }
            }
        });

        //$(document).ready(getWeeks(0));
        $(function() {
            $('#landingdatetime').datetimepicker({ language: '{{str_replace('_', '-', App::getLocale())}}',
                format: 'dd-mm-yyyy hh:ii',
                minuteStep: 30,
                autoclose: true,
                startView: 1});

            $('#departuredatetime').datetimepicker({ language: '{{str_replace('_', '-', App::getLocale())}}',
                format: 'dd-mm-yyyy hh:ii',
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
            $("[name='Book']").css('opacity', 0.2);
            cont = false;
            culture = '{{App::getLocale()}}';
            contractid = {{$models[0]->id}};
            periodid = 0;
            houseid = {{$models[0]->houseid}};
            url = '/ajax/getweeks/' + houseid + '/' + culture + '/' + offset + '/' + periodid + '/' + contractid  + '/8';
            new $.getJSON(url, function(periods)
            {
                content = '<table class="table table-striped"><tr><th>{{__('Period')}}</th><th>{{__('Choose')}}</th><th>{{__('Guests')}}</th></tr>';
                //We add a large number to ensure the index is positive
                if (periods.warning == 'no records') alert('nothing found');
                periodchunk[offset+1000] = periods;
                periodchunk.forEach(periods => {
                    periods.forEach(period => {
                        if (period.warning == 'lower limit reached') adjustOffsetMinus  = false;
                        rate = period.rate;
                        baseprice[period.id] = period.baseprice;
                        personprice[period.id] = period.personprice;
                        basepersonscommon = basepersons[period.id] = period.basepersons;
                        maxpersonscommon = period.maxpersons;
                        from[period.id] = period.from;
                        to[period.id] = period.to;
                        checked = '';
                        style = '';
                        if (checkedstatus[period.id] != undefined) period.chosen = checkedstatus[period.id];
                        if (!period.persons) period.persons = 2;
                        if (period.chosen) checked = ' checked = "checked"';
                        free = '<input class="" onClick="setFinalprice()"' + checked + ' name="checkedWeeks[' + period.id + ']" id="checkedWeeks_' + period.id + '" type="checkbox" value="'+period.id+'">';
                        if (period.committed && !period.chosen) {
                            free = '{{__('Occupied')}}';
                            style = ' style = "opacity: 0.40"';
                            content += '<tr' + style + '><td colspan="3">' + free + ': ' + period.periodtext + '</td></tr>';
                        }

                        //content += '<tr' + style + '><td colspan="2">' + free + period.periodtext + '</td></tr>';
                        else {
                            fieldname = "persons_" + period.id;
                            addreduce = '<input  name="'+fieldname+'" type="hidden" value="'+period.persons+'" id="'+fieldname+'">';
                            addreduce += '<span id=span_'+period.id+'>'+period.persons+'</span>';
                            addreduce += '<span style="margin-left: 10px" class="glyphicon glyphicon-plus rounded-circle" onclick="addPersonsPeriods(1, ' + period.id + ');return false;"></span>';
                            addreduce += '<span style="margin-left: 10px" class="glyphicon glyphicon-minus rounded-circle" onclick="addPersonsPeriods(-1, ' + period.id + ');return false;"></span>';
                            content += '<tr' + style + '><td onclick="togglecheck('+period.id+');">' + period.periodtext + '</td><td>' + free + '</td><td><div id="addreduce_' + period.id + '" style="border: none; border-color: red">' + addreduce + '</div></td></tr>';
                        }
                    });
                });
                content += '</tr>';
                $('#showweeks').html(content);
                if ($('#hiddenprice').val() == 0) setFinalprice();
                setTimeout(function(){cont = true; }, 1000);
                if ($("#calendar").scrollTop() < 10)  $("#calendar").scrollTop(10);
                $("[name='Book']").css('opacity', 1);
            });
        }

        function togglecheck(id)
        {
            checkbox = $('#checkedWeeks_' + id);
            if (checkbox.prop('checked')) $('#checkedWeeks_' + id).prop("checked", false);
            else checkbox.prop("checked", true);
            setFinalprice();
            checkedstatus[id] = checkbox.prop('checked');
        }

        function addPersons(increment)
        {
            persons = Math.round(Number($('#persons').val()));
            persons = persons + increment;
            persons = Math.min(persons, maxpersonscommon);
            persons = Math.max(persons, basepersonscommon);
            $('#persons').val(persons);
            $('#persons_0').text(persons);

            $("input:checked").each(function() {
                $('#persons_' + this.value).val(persons);
                $('#span_' + this.value).text(persons);
            });

            setFinalprice();
        }

        function addPersonsPeriods(increment, id)
        {
            persons = Number($('#persons_'+id).val());
            persons = persons + increment;
            persons = Math.min(persons, maxpersonscommon);
            persons = Math.max(persons, basepersonscommon);
            $('#persons_'+id).val(persons);
            $('#span_'+id).text(persons);
            weeks = 0;
            sum = 0;
            $("input:checked").each(function() {
                weeks++;
                sum += Number($('#persons_' + this.value).val());
            });
            $('#persons').val(0.01*Math.round(100*sum/weeks));
            $('#persons_0').text(0.01*Math.round(100*sum/weeks));
            setFinalprice();
        }

        function lessWeeks()
        {
            offsetminus = 0;
            offsetplus = 0;
            periodchunk = [];
            usedheight = 50;
            console.log('Usedheight: '+usedheight);
            console.log(100*$( "#calendar" ).scrollTop()/usedheight);
            $("#calendar").scrollTop(10);
            return 0;
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

            //Calculate the price in the house currency, check consequtive periods and revert if not consequtive
            periodfrom = [];
            periods = 0;
            lastto = 0;
            $("input:checked").each(function() {
                price += baseprice[this.value] + (Number($('#persons_'+this.value).val()) - basepersons[this.value])*personprice[this.value];
                periods = periods + 1;
                if (periods > 1)
                {
                    if (lastto != from[this.value])
                    {
                        $('#notAdjecent').modal('show');
                        togglecheck(this.value);
                        newPrice();
                    }

                }
                lastto = to[this.value];
            });

            if ((periods > 1) && (!showadjustguestsinfo)) {
                // Show modal informing customer that the number of guest can be set for each period.
                showadjustguestsinfo = true;
                $('#adjustguestsinfo').modal();
            }

            $("input:not(:checked)").each(function() {
                if (showadjustguestsinfo) {
                    $('#addreduce_' + this.value).css('border', 'none');
                }
            });

            $("input:checked").each(function() {
                if (showadjustguestsinfo) {
                    $('#addreduce_' + this.value).css('border', 'solid').css('border-color', 'red');
                }
            });

            @if(sizeof($currencySelect) > 0)
                rate = rates[$('#currencyid').val()];
                console.log('Currencyid is: '+$('#currencyid').val());
            @endif

            $('#hiddenprice').val(rate*price);
            $('#ratechosen').val(rate);
            return rate*price;
        }

    </script>
@endsection
