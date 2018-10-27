@extends('layouts.app')
@section('content')
    <h3>{{__('Contract')}}</h3>
    <div class="table-responsive">
        @foreach($fields as $field)
        <dl class="dl-horizontal row">
            <dt class="col-md-5 col">
                {{ucfirst($field)}}
            </dt>
            <dd class="col">
                {{$models[0]->withBelongsTo($field)}}
            </dd>
        </dl>
        @endforeach
        {!! $models->appends(\Request::except('page'))->render() !!}
        <a href="/customer/edit/{{$models[0]->id}}">Edit Customer</a>
    </div>
@endsection