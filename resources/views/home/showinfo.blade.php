@extends('layouts.app')
@section('content')
    <div class="table-responsive table-sm">
        <div class="row col-md-12"  style="position: fixed; margin-bottom: 20px; margin-left: -3px;">
            <!--<div dusk="click-order" class="btn btn-success row col-md-12"  data-offset-top="30px" role="alert"  id="checkvacanciesappear" onclick="$('#vacancyPrMonth').toggle();" style="display: none; ">
            Changes by wish of customer -->
            <div dusk="click-order" class="btn btn-success row col-md-12"  data-offset-top="30px" role="alert"  id="checkvacanciesappear" onclick="window.location = '/home/checkbookingslist?menupoint=10020';" style="display: none; ">
                {{ (config('app.testmessage'))?__(config('app.testmessage')):__('Check prices and book') }}
            </div>
            <div class="row col-md-12" id="vacancyPrMonth" style="display: none;">
                @include('partials.search', ['houseid' => session('defaultHouse' , 1)])
            </div>
        </div>
        <div style="margin-top: 30px">
            {!! $info !!}
        </div>
    </div>
    <script>
        $("#checkvacanciesappear").fadeIn(5000);
        $('#vacancyPrMonth').draggable;
    </script>
@endsection
