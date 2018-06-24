@extends('layouts.app')
@section('content')
    <h3>Customer</h3>
    <div class="table-responsive">
        @foreach($fields as $field)
        <dl class="dl-horizontal row">
            <dt class="col-md-5 col">
                {{ucfirst($field)}}
            </dt>
            <dd class="col">
                {{$model->$field}}
            </dd>
        </dl>
        @endforeach
        <a href="/customer/edit/{{$model->id}}">Edit Customer</a>
    </div>
@endsection