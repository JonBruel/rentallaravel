@extends('layouts.app')
@section('content')
    <h3>Customers</h3>
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
                <th>@sortablelink('name')</th>
                <th>@sortablelink('address1')</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach($models as $model)
                <tr>
                    <td>
                        @include('partials.detail_edit_delete', ['path' => 'customer', 'id' => $model->id, 'params' => $params])
                    </td>
                    <td>{{ $model->name }}</td>
                    <td>{{ $model->address1 }}</td>
                    <td><a href="/impersonate/take/{{ $model->id }}">Impersonate</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
        {!! $models->appends(\Request::except('page'))->render() !!}
    </div>

@endsection