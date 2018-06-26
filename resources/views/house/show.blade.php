@extends('layouts.app')
@section('content')
    <h3>Customer</h3>
    <div class="table-responsive">
        <a href="/house/edit/{{$model->id}}">Edit House</a>
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
        <a href="/house/edit/{{$model->id}}">Edit House</a>
    </div>
@endsection