
@extends('layouts.app')
@section('content')
    <h3>{{ __('Edit house') }}</h3>
    <div class="table-responsive">
        {!! Form::model($models[0], ['action' => ['HouseController@update', $models[0]]]) !!}
        {!! Form::submit('Save changes',['class' => "btn btn-primary"]); !!}
        <br />
        <br />

        @include('partials.two_column_edit_1', ['model' => $models[0], 'errors' => $errors, 'fields' => $fields, 'vattr' => $vattr])
        {!! Form::submit('Save changes',['class' => "btn btn-primary"]); !!}
        <a href="/house/edithousehtml/{{$models[0]->id}}" class="btn btn-success" role="button">{{__('Add/edit text/pictures')}}</a>

        {!! Form::close() !!}
        <br />
        <br />
        <strong><p><?php echo __('Adjust marker to right position and press Update or Save') . ':'; ?></p></strong>
        <div id="map_canvas" style="maxwidth: 740px; height: 600px"></div>
    </div>

@include('partials.client_validation')
@endsection
@section('scripts')
    <link href="{{ asset('/vendor/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css', true)}}" rel="stylesheet">
    <script type="text/javascript" src="{{ asset('/vendor/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js', true)}}"></script>
    <script type="text/javascript" src="//maps.googleapis.com/maps/api/js?key={{ $googlekey }}"></script>
    <script  type="text/javascript" src="/vendor/rental/markerwithlabel.js"></script>
    <style>
        .labels {
            color: red;
            background-color: white;
            font-family: "Lucida Grande", "Arial", sans-serif;
            font-size: 10px;
            borderRadius: 20px;
            font-weight: bold;
            text-align: left;
            width: auto;
            height: auto;
            border: 2px solid black;
            white-space: nowrap;
        }
    </style>
    <script type="text/javascript">
        culture = "{{App::getLocale()}}";
        culture = culture.replace("_", "-");

        //Formatter used to convert from number to string, opposite the ParseString below
        Formatter = new Intl.NumberFormat(culture,{ minimumFractionDigits: 12,  maximumFractionDigits: 12});
        decimalseparator = Formatter.format(1.01).substring(1,2);
        thousandsseparator = Formatter.format(1000).substring(1,2);

        /*
        This function converts a string representation of a number to a number
         */
        function ParseString(string) {
            string = string.replace(thousandsseparator, '');
            string = string.replace(decimalseparator, '.');
            return Number(string);
        }

        function initialize()
        {
            var houseinfo = {!! $housefields !!};
            veryshortdescription = houseinfo.veryShortDescription;
            id = houseinfo.id;
            housename = houseinfo.name;
            latitude = ParseString(houseinfo.latitude);
            longitude = ParseString(houseinfo.longitude);
            maxpersons = houseinfo.maxpersons;
            zoom = 14;

            var mapOptions = {
                zoom: 14,
                center: new google.maps.LatLng(latitude, longitude),
                zoomControl: true
            }


            var map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);
            map.setMapTypeId(google.maps.MapTypeId.HYBRID);

            //var map = new GMap2(document.getElementById("map_canvas"));
            //The default values lat-lon are 45-10, in that case we show most of the world!
            if ((latitude == 45) || (longitude == 10)) zoom = 3;
            map.setCenter(new google.maps.LatLng(latitude, longitude), zoom);

            continuezoom = true;



            google.maps.event.addListenerOnce(map, 'idle', function()
            {
                getvalues();
            });

            function getvalues()
            {

                var x1 = map.getBounds().getSouthWest().lat();
                var y1 = map.getBounds().getSouthWest().lng();
                var x2 = map.getBounds().getNorthEast().lat();
                var y2 = map.getBounds().getNorthEast().lng();

                var url = '/ajax/ajaxlisthouses/x1/' + x1 + '/y1/' + y1 + '/x2/' + x2 + '/y2/' + y2 + '/houseid/' + id;
                var url = '/ajax/ajaxlisthouses/x1/' + x1 + '/y1/' + y1 + '/x2/' + x2 + '/y2/' + y2;
                new $.getJSON(url, function(houses)
                {
                    var house = houses[0];
                    imax = house.length;
                    x1 = house.x1;
                    y1 = house.y1;
                    x2 = house.x2;
                    y2 = house.y2;

                    centerx = (x2+x1)/2;
                    centery = (y2+y1)/2;

                    for(i=0;i<imax;i++)
                    {
                        house = houses[i];

                        if(house.hasOwnProperty('longitude'))
                        {
                            housename = house.name;
                            maxpersons = 6;
                            if (house.maxpersons) maxpersons = house.maxpersons;
                            maxpersons = ''+maxpersons;
                            housenamearray = housename.split(' ');
                            id = house.id;

                            markeroptions = {
                                position:  new google.maps.LatLng(house.latitude,house.longitude),
                                draggable: true,
                                map: map,
                                title: "Maxpersons: "+maxpersons,
                                icon: 'http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld='+maxpersons+'|00ffff|000000'
                            }

                            @if($administrator)
                                markeroptions.draggable = true;
                            @endif

                            var mytext = '<br/>' + housename;
                            mytext = mytext + '<br/>'+house.veryShortDescription;

                            marker = new google.maps.Marker(markeroptions);

                            google.maps.event.addListener(marker,"dragend",function(event){
                                position = this.getPosition();
                                lat = position.lat();
                                lng = position.lng();
                                $('#longitude').val(Formatter.format(lng));
                                $('#latitude').val(Formatter.format(lat));
                            });
                        }
                    }
                });

            }
        }
    </script>
    <body onload="initialize()" onunload="" style="background-color:Transparent">
@endsection