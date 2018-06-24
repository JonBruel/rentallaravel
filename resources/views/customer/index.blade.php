@extends('layouts.app')
@section('content')
    <h3>Customers</h3>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>@sortablelink('id')</th>
                <th>@sortablelink('name')</th>
                <th>@sortablelink('address1')</th>
            </tr>
            </thead>
            <tbody>
            @foreach($models as $model)
                <tr>
                    <td><a href="/customer/show/{{ $model->id }}">{{ $model->id }}</a></td>
                    <td>{{ $model->name }}</td>
                    <td>{{ $model->address1 }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        {!! $models->appends(\Request::except('page'))->render() !!}
    </div>
@endsection