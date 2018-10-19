@extends('layouts.app')
@section('content')
    <h3>{{__('Houses')}}</h3>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>@sortablelink('id')</th>
                <th>@sortablelink('name')</th>
                <th>@sortablelink('address1')</th>
                <th>@sortablelink('ownerid')</th>
            </tr>
            <tr>
                <form id="Filter" action="{{Session::get('sanitizedpath')}}" method="get">
                    <td>
                        <button type="submit" class="btn btn-success glyphicon glyphicon-search" name="Search" value="Search"></button>
                        <input type="hidden" name="sort" value="{{(array_key_exists('sort', $search))?$search['sort']:''}}" />
                        <input type="hidden" name="order" value="{{(array_key_exists('order', $search))?$search['order']:''}}" />
                    </td>

                <td>
                    {!! Form::text('name',(array_key_exists('name', $search))?$search['name']:'',null,['class' => 'form-control']) !!}
                </td>
                <td>
                    {!! Form::text('address1',(array_key_exists('address1', $search))?$search['address1']:'',null,['class' => 'form-control']) !!}
                </td>
                <td>
                    {!! Form::select('ownerid',$owners,(array_key_exists('ownerid', $search))?$search['ownerid']:'',['class' => 'form-control', 'style' => 'padding: 1px 0 3px 10px;']) !!}
                </td>
                </form>
            </tr>
            </thead>
            <tbody>
            @foreach($models as $model)
                <tr>
                    <td>
                        @include('partials.detail_edit_delete', ['path' => 'house', 'id' => $model->id])
                    </td>
                    <td>{{ $model->name }}</td>
                    <td>{{ $model->address1 }}</td>
                    <td>({{ $model->ownerid }}){{ $model->customer->name }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        {!! $models->appends(\Request::except('page'))->render() !!}
    </div>
@endsection