@extends('layouts.app')
@section('content')
    <div id="galleryCarousel" class="carousel slide" data-ride="carousel">

        <div class="carousel-inner" role="listbox">
            @foreach ($picturearray['path'] as $key => $pictureurl)
                <?php
                    $active = '';
                    if ($key == 0) $active = 'active';
                ?>
                <figure class="carousel-item {{$active}}" style="background-color: #5a6268;">
                    <img src="{{$pictureurl}}" alt="{{@trans($picturearray['text'][$key])}}" class="d-block mx-auto"
                    style="height: 555px; max-width: 100%; ">
                    <div class="carousel-caption" style="background: radial-gradient(grey, #5a6268); top: 0;  opacity: 0.80; bottom: auto; color: darkblue; padding-top: 5px; padding-bottom: 5px">
                        <h4 style="margin-top: 0px">{{ __($picturearray['text'][$key])}}</h4>
                    </div>
                </figure>
            @endforeach
        </div>

        <a class="carousel-control-prev" href="#galleryCarousel" role="button" data-slide="prev">
            <span style="font-size: x-large; color: white" class="glyphicon glyphicon-chevron-left" area-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#galleryCarousel" role="button" data-slide="next">
            <span style="font-size: x-large; color: white" class="glyphicon glyphicon-chevron-right" area-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>

    </div>

@endsection
<!--
        <div class="carousel-inner" role="listbox">
            @foreach ($picturearray['path'] as $key => $pictureurl)
    <?php
    $active = '';
    if ($key == 0) $active = 'active';
    ?>
            <figure class="carousel-item {{$active}}" style="background-color: #5a6268">
                    <img src="{{$pictureurl}}" alt="{{@trans($picturearray['text'][$key])}}" class="d-block mx-auto"
                    style="height: 555px; max-width: 740px">
                    <div class="carousel-caption text-success d-none d-sm-block" style="background-color: #9fcdff; height: 20px; vertical-align: center">
                        <h4 style="margin-top: -15px">{{ __($picturearray['text'][$key])}}</h4>
                    </div>
                </figure>
            @endforeach
        </div>
-->