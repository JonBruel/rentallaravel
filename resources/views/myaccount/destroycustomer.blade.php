
@extends('layouts.app')
@section('content')
    <h3>{{__('All records deleted.')}}</h3>
    <div class="alert alert-success border border-primary">{{$message}}</div>
@endsection