@extends('layouts.app')
@section('content')
    <h3>Houses</h3>
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
                    <td>
                        @include('partials.detail_edit_delete', ['path' => 'house', 'id' => $model->id, 'params' => $params])
                    </td>
                    <td>{{ $model->name }}</td>
                    <td>{{ $model->address1 }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        {!! $models->appends(\Request::except('page'))->render() !!}
    </div>
@endsection