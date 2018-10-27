@extends('layouts.app')
@section('content')
    <h3>{{__('List or edit periods')}} @if(sizeof($houses) == 2){{$houses[1]}}@endif</h3>

    <br/>
    <div class="table-responsive">
        <table class="table table-striped">
            @if(sizeof($houses) > 2)
            <tr   style="border-style: solid solid solid solid; border-width:4px 4px 4px 4px; border-color:green;">
                <td colspan="7">
                    <form id="Filter" action="{{Session::get('sanitizedpath')}}" method="get">
                        {!! Form::select('houseid',$houses,(array_key_exists('houseid', $search))?$search['houseid']:'',['class' => 'form-control col-md-4', 'style' => 'padding: 1px 0 3px 10px;', 'onchange' => 'this.form.submit();']) !!}
                    </form>
                </td>
            </tr>
            @endif
            <tr>
                <td>
                    {{__('Year')}}
                </td>
                <td>
                    {{__('Week')}}
                </td>
                <td>
                    {{__('Period')}}
                </td>
                @if(sizeof($houses) > 2)
                    <td>
                        {{__('House')}}
                    </td>
                @endif
                <td>
                    {{__('Base price')}}
                </td>
                <td>
                    {{__('Person price')}}
                </td>
                <td>
                    {{__('Edit')}}
                </td>
            </tr>
            <tbody>
            @foreach($models as $model)
                <tr style="{{(($model->committed>0) || ($model->prepaid>0))?'background-color: #FF9191;':''}}">
                    <td>
                        {{$model->from->year}}
                    </td>
                    <td>
                        {{$model->from->weekOfYear}}
                    </td>
                    <td>
                        {{$model->getEnddays()}}
                    </td>
                    @if(sizeof($houses) > 2)
                        <td>
                            {{$model->house->name}}
                        </td>
                    @endif
                    <td>
                        {{$model->house->currency->currencysymbol}} {{$model->baseprice}}
                    </td>
                    <td>
                        {{$model->house->currency->currencysymbol}} {{$model->personprice}}
                    </td>
                    <td>
                        @if(!(($model->committed>0) || ($model->prepaid>0)))
                        <a href="/house/editperiod/{{ $model->id }}" title="{{__('Edit')}}" data-toggle="tooltip"><span class='glyphicon glyphicon-pencil'></span></a>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        {!! $models->appends(\Request::except('page'))->render() !!}
    </div>
@endsection