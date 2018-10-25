
@extends('layouts.app')
@section('content')
    <h3>{{ __('Create periods') }}</h3>
    <div class="table-responsive">
        <table class="table table-striped">
            {!! Form::open(['name' => 'start', 'url' => '/house/createperiods', 'autocomplete' => 'off']) !!}
            <tr>
                <td colspan="7">
                    <p>{{ __('createperiods.explaintext')}}</p>
                </td>
            </tr>
            <tr>
                <td colspan="3">
                     {!! Form::label('houseid', __('House').':', ['class' => 'col-md-9']) !!}
                </td>
                <td colspan="1">

                    {!! Form::label('seasons', __('Number of seasons').':', ['class' => 'col-md-9']) !!}
                </td>
                <td colspan="2">
                     {!! Form::label('periodlength', __('Period length').':', ['class' => 'col-md-9']) !!}
                </td>
                <td colspan="1">

                </td>
            </tr>
            <tr>
                <td colspan="3">
                     {!! Form::select('houseid', $houses, $houseid, ['class' => 'form-control col-md-12', 'style' => 'padding: 1px 0 3px 10px;']) !!}
                </td>
                <td colspan="1">
                     {!! Form::text('seasons', $seasons,  ['class' => 'form-control col-md-12', 'style' =>"height: 28px"]) !!}
                </td>
                <td colspan="2">
                     {!! Form::text('periodlength', $periodlength,  ['class' => 'form-control col-md-12', 'style' => "height: 28px"]) !!}
                 </td>
                <td colspan="1">
                    {!! Form::submit(__('Prepare'),['class' => "btn btn-primary", 'name' => 'prepare']); !!}
                </td>
            </tr>

            <tr>
                <td colspan="7">
                    <p>{{ __('createperiods.fillintext')}}</p>
                </td>
            </tr>

            <tr>
                <th>
                    {!! Form::hidden('houseid', $houseid) !!}
                </th>
                <th>
                    <div title="{{__('createperiod.seasonstart')}}"  data-toggle="tooltip">{{__('Season start')}}</div>
                </th>
                <th>
                    <div title="{{__('createperiod.seasonend')}}"  data-toggle="tooltip">{{__('Season end')}}</div>
                </th>
                <th>
                    <div title="{{__('createperiod.basepersons')}}"  data-toggle="tooltip">{{__('Base persons')}}</div>
                </th>
                <th>
                    <div title="{{__('createperiod.maxpersons')}}"  data-toggle="tooltip">{{__('Max. persons')}}</div>
                </th>
                <th>
                    <div title="{{__('createperiod.baseprice')}}"  data-toggle="tooltip">{{__('Base price')}}</div>
                </th>
                <th>
                    <div title="{{__('createperiod.personprice')}}"  data-toggle="tooltip">{{__('Person price')}}</div>
                </th>
            </tr>
            @if($errors)
            <tr>
                <td colspan="7">
                    <ul>
                        @foreach ($errors as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </td>
            </tr>
            @endif
            @for($i=0;$i<$seasons;$i++)
                <tr>
                    <th>{{ __('Season') . ' ' . $i }} </th>
                    <td>{!! Form::text('data[seasonstart][]', $data['seasonstart'][$i],  ['class' => 'form-control col-md-12', 'style' =>"height: 28px", 'onChange' => 'updatestartchanged('.$i.')', 'id' => 'data_seasonstart_'.$i]) !!}</td>
                    <td>{!! Form::text('data[seasonend][]', $data['seasonend'][$i],  ['class' => 'form-control col-md-12', 'style' =>"height: 28px", 'onChange' => 'updateendchanged('.$i.')', 'id' => 'data_seasonend_'.$i]) !!}</td>
                    <td>{!! Form::text('data[basepersons][]', $data['basepersons'][$i],  ['class' => 'form-control col-md-12', 'style' =>"height: 28px"]) !!}</td>
                    <td>{!! Form::text('data[maxpersons][]', $data['maxpersons'][$i],  ['class' => 'form-control col-md-12', 'style' =>"height: 28px"]) !!}</td>
                    <td>{!! Form::text('data[baseprice][]', $data['baseprice'][$i],  ['class' => 'form-control col-md-12', 'style' =>"height: 28px"]) !!}</td>
                    <td>{!! Form::text('data[personprice][]', $data['personprice'][$i],  ['class' => 'form-control col-md-12', 'style' =>"height: 28px"]) !!}</td>
                </tr>
            @endfor
            <tr>
                <th>&nbsp;</th>
                @for($i=0;$i<6;$i++)
                    <th>{{ $year[$i] }}</th>
                @endfor
            </tr>
            <tr>
                <th>{{ __('Easter') }}:</th>
                @for($i=0;$i<6;$i++)
                <td>{{ $easter[$i] }}</td>
                @endfor
            </tr>
            <tr>
                <td colspan="6"></td>
                <td>
                    {!! Form::hidden('test', 'no', ['id' => 'test']) !!}
                    {!! Form::submit(__('Create periods'),['class' => "btn btn-primary", 'name' => 'insertcreateperiods', 'onclick' => '$("#test").val("yes");']) !!}
                </td>
            </tr>
            {{! Form::close()}}
        </table>
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
        seasons = {{$seasons}}


        /*
        This function converts a string representation of a number to a number
         */
        function ParseString(string) {
            string = string.replace(thousandsseparator, '');
            string = string.replace(decimalseparator, '.');
            return Number(string);
        }

        function updateendchanged(i)
        {
            if (!blockexecution)
            {
                blockexecution = true;
                for (j=i;j<seasons;j++)
                {
                    newval = $('#data_seasonend_'+(i)).val();
                    if (j < seasons - 1)
                    {
                        $('#data_seasonstart_'+(j+1)).val(newval);
                        $('#data_seasonend_'+(j+1)).val(newval);
                    }

                }

                blockexecution = false;
            }
        }

        function updatestartchanged(i)
        {
            if (!blockexecution)
            {
                blockexecution = true;
                for (j=i;j<seasons;j++)
                {
                    newval = $('#data_seasonstart_'+i).val();
                    $('#data_seasonend_'+j).val(newval);
                    if (j < seasons - 1) $('#data_seasonstart_'+(j+1)).val(newval);
                }
                blockexecution = false;
            }

        }

        $(function() {
            @for($i=0;$i<$seasons;$i++)
            $('#data_seasonstart_{{$i}}').datetimepicker({ language: '{{str_replace('_', '-', App::getLocale())}}',
                format: 'yyyy-mm-dd',
                minView: 2,
                autoclose: true,
                startView: 2});

            $('#data_seasonend_{{$i}}').datetimepicker({ language: '{{str_replace('_', '-', App::getLocale())}}',
                format: 'yyyy-mm-dd',
                minView: 2,
                autoclose: true,
                startView: 2});
            @endfor
        });


    </script>
@endsection