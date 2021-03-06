@extends('layouts.app')
@section('content')
    <h3>{{__('Batch tasks') }}</h3>
    <div class="table-responsive table-sm">
        <table class="table table-striped">
            <thead>
            @if((sizeof($owners) > 2) || (sizeof($houses) > 2))
                <tr style="border-style: solid solid solid solid; border-width:4px 4px 4px 4px; border-color:green;">
                    <form id="Filter" action="{{Session::get('sanitizedpath')}}" method="get">
                        <td>

                        </td>

                        <td colspan="2">

                        </td>
                        <td>
                            @if(sizeof($owners) > 2)
                                {!! Form::select('ownerid',$owners,(array_key_exists('ownerid', $search))?$search['ownerid']:'',['class' => 'form-control', 'style' => 'padding: 1px 0 3px 10px;', 'onchange' => 'this.form.submit();']) !!}
                            @endif
                        </td>
                        <td>
                            @if(sizeof($houses) > 2)
                                {!! Form::select('houseid',$houses,(array_key_exists('houseid', $search))?$search['houseid']:'',['class' => 'form-control', 'style' => 'padding: 1px 0 3px 10px;', 'onchange' => 'this.form.submit();']) !!}
                            @endif
                         </td>
                    </form>
                </tr>
            @endif
            <tr>
                <th>{{ __('Edit') }}</th>
                <th>{{ __('Batch task name') }}</th>
                <th>{{ __('Triggered by') }}</th>
                <th>{{ __('Limited to Owner') }}</th>
                <th>{{ __('Restricted to house') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($models as $model)
                <tr>
                    <td>
                        <a href="/setup/editbatchtask/{{ $model->id }}?ownerid={{ $ownerid }}" title="{{__('Edit')}}" data-toggle="tooltip"><span class='glyphicon glyphicon-pencil'></span></a>
                    </td>
                    <td>{{ __($model->name) }} ({{$model->id}})</td>
                    <td>{{ $model->posttypeid }}</td>
                    <td>{{ $model->customer->name }}</td>
                    <td>{{ $model->house->name }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection