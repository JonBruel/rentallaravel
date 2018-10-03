@extends('layouts.app')
@section('content')
    <h3>{{__('Account posts')}}</h3>
    <br /><br />
    <div class="table-responsive">
        <?php $contractid = -1; ?>
        @foreach($models as $model)
            @if(($contractid != $model->contractid) || ($model->contractid == ''))
                <?php $contractid = $model->contractid; ?>
                {{__('Contract number')}}: {{$model->contractid}} {{__('periode')}}: {{$model->contract->getPeriodtext(\App::getLocale()) }}:<br/>
                {!! $model->contract->getAccountposts() !!}
            @endif
        @endforeach
    </div>
@endsection