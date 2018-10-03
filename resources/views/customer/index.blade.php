@extends('layouts.app')
@section('content')
    <h3>{{ __('Customers') }}</h3>
    <br />
    <a href='/customer/create'>
        <span class='glyphicon glyphicon-plus'></span>
    </a>
    <br /><br />
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>@sortablelink('id')</th>
                <th></th>
                <th>@sortablelink('name')</th>
                <th>@sortablelink('address1')</th>
                <th>@sortablelink('email')</th>
                <th>Actions</th>
            </tr>
            <tr>
                <form id="Filter" action="{{Session::get('sanitizedpath')}}" method="get">
                    <td>
                        {!! Form::submit(__('Search'),['class' => 'form-control']) !!}
                        <input type="hidden" name="sort" value="{{(array_key_exists('sort', $search))?$search['sort']:''}}" />
                        <input type="hidden" name="order" value="{{(array_key_exists('order', $search))?$search['order']:''}}" />
                    </td>
                    <td></td>
                    <td>
                        {!! Form::text('name',(array_key_exists('name', $search))?$search['name']:'',null,['class' => 'form-control']) !!}
                    </td>
                    <td>
                        {!! Form::text('address1',(array_key_exists('address1', $search))?$search['address1']:'',null,['class' => 'form-control']) !!}
                    </td>
                    <td>
                        {!! Form::text('email',(array_key_exists('email', $search))?$search['email']:'',null,['class' => 'form-control']) !!}
                    </td>
                </form>
            </tr>
            </thead>
            <tbody>
            @foreach($models as $model)
                <tr>
                    <td>
                        @include('partials.detail_edit_delete', ['path' => 'customer', 'id' => $model->id, 'params' => $params])
                    </td>
                    <td>
                        <a href="/impersonate/take/{{ $model->id }}"  title="{{__('See site as: ')}} {{$model->name}}"  data-toggle="tooltip"><span class='glyphicon glyphicon-user'></span></a>
                    </td>
                    <td>{{ $model->name }}</td>
                    <td>{{ $model->address1 }}</td>
                    <td>{{ $model->email }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        {!! $models->appends(\Request::except('page'))->render() !!}
    </div>

@endsection