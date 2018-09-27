@extends('layouts.app')
@section('content')
    <h3>Standard emails</h3>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
            <tr>
                <form id="Filter" action="{{Session::get('sanitizedpath')}}" method="get">
                    <td>

                    </td>

                    <td colspan="1">

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
            <tr>
                <th>{{ __('Edit') }}</th>
                <th>{{ __('Email description') }}</th>
                <th>{{ __('Limited to Owner') }}</th>
                <th>{{ __('Restricted to house') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($models as $model)
                <tr>
                    <td>
                        <a href="/setup/editstandardemail/{{ $model->id }}?ownerid={{ $ownerid }}" title="{{__('Edit')}}" data-toggle="tooltip"><span class='glyphicon glyphicon-pencil'></span></a>
                    </td>
                    <td>{{ $model->description }}</td>
                    <td>{{ $model->customer->name }}</td>
                    <td>{{ $model->house->name }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection