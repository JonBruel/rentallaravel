@extends('layouts.app')
@section('content')
    <h3>{{__('Bookings')}}</h3>
    <style>
        .occupied {
            background: #FF9191;
            margin-left: 15px;
        }
        .family {
            background: #00cc99;
            margin-left: 15px;
        }
        .notoffered {
            background: #005588;
            margin-left: 15px;
        }
        .halfday {
            background: #88ff66;
            margin-left: 15px;
        }
    </style>

    <p class="header"><strong>{{$house->name}}</strong></p>
    <p class="header" style="margin-right:70px">{{ __('The bookings from now on and 52 weeks ahead.')}}</p>
    <p>
    <div class="row" id="occupied" style="margin-bottom: 5px">
        <div class="col-2 occupied"></div><div class="col-9">{{ __('Pink weeks are occupied.') }}</div>
    </div>

    <div class="row" id="notoffered" style="margin-bottom: 5px">
        <div class="col-2 notoffered"></div><div class="col-9">{{ __('Blue days are not yet scheduled for rental, but you are welcome to make an equiry.') }}</div>
    </div>

    <div class="row" id="halfday" style="margin-bottom: 5px">
        <div class="col-2 halfday"></div><div class="col-9">{{ __('Green days are change days.') }}</div>
    </div>

    <div class="row" id="family" style="margin-bottom: 5px">
        <div class="col-2 family"></div><div class="col-9">{{__('Turquoise days are for private use.')}} </div>
    </div>
    <a class="btn btn-success col-md-12" href="/home/checkbookings?menupoint=10020&listtype=list">{{__('Change to list view')}}</a>
    </p>
    <br />
    <p class="header" id="tip"><?php echo __('For prices: move cursor to date, click to order.');?>
    <br/>
    <div class="row align-items-center justify-content-center">
        <div class="col-md-4 pagination-centered">{!! $pager->links('vendor/pagination/bootstrap-4', ['elements' => $elements, 'offset' => $offset]) !!}</div>
    </div>


    <div id="calendar">
        <div class="row">
            <div class="col-md-2">{{ __('Period')}}: </div>
            <div class="col-md-10" id="period">{{ __('For prices: move cursor to date, click to order.') }}</div>
        </div>
        <div class="row" style="min-height: 3rem">
            <div class="col-md-2">{{ __('Price')}}: </div>
            <div class="col-md-10" id="price"></div>
        </div>
        <div class="row justify-content-center" style="margin-top: 5px">
            @for($i = 0; $i < 12; $i++)
                <div class="col-md-4  justify-content-center" height="160" valign="top">{!! $cal[$i] !!}</div>
                @if(($i > 0 ) and (($i+1) % 3 == 0))
                    </div><div class="row justify-content-center" style="margin-top: 5px">
                @endif
            @endfor
        </div>
    </div>
    <p class="header" id="tipfixed">{{ __('For prices: move cursor to date, click to order.') }}</p>
@endsection
@section('scripts')
<script>
    if ($(".notoffered").length < 2) $("#notoffered").toggle();
    if ($(".occupied").length < 2) $("#occupied").toggle();
    if ($(".halfday").length < 2) $("#halfday").toggle();
    if ($(".family").length < 2) $("#family").toggle();
</script>
@endsection