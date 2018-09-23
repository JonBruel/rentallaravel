@extends('layouts.app')
@section('content')
    <h3>{{__('Account posts')}}</h3>
    <br /><br />
    <div class="table-responsive">
        @foreach($models as $model)
            {{__('Contract number')}}: {{$model->id}} {{__('periode')}}: {{$model->contract->getPeriodtext(\App::getLocale()) }}:<br/>
            {!! $model->contract->getAccountposts() !!}
        @endforeach
    </div>
@endsection