@extends('layouts.app')
@section('content')
    <h3>{{__('House')}}</h3>
    <div class="table-responsive">
        <a href="/house/edit/{{$models[0]->id}}">Edit House</a>
        @foreach($fields as $field)
        <dl class="dl-horizontal row">
            <dt class="col-md-5 col">
                {{ucfirst($field)}}
            </dt>
            <dd class="col">
                {{$models[0]->$field}}
            </dd>
        </dl>
        @endforeach
        <a href="/house/edit/{{$models[0]->id}}">Edit House</a>
        {!! $models->appends(\Request::except('page'))->render() !!}
    </div>
@endsection