@extends('layouts.app')
@section('content')
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

        function initialize()
        {
            var mytext = {!!  $housefields !!} ;
            veryshortdescription = mytext.veryShortDescription;
            id = mytext.id;
            housename = mytext.name;
            latitude = mytext.latitude;
            longitude = mytext.longitude;


            var mapOptions = {
                zoom: 14,
                center: new google.maps.LatLng(latitude, longitude),
                zoomControl: true
            }


            var map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);
            map.setMapTypeId(google.maps.MapTypeId.HYBRID);

            continuezoom = true;
            infowindowclosed = true;

            google.maps.event.addListenerOnce(map, 'idle', function()
            {
                getvalues();
            });


            google.maps.event.addListener(map, "moveend", function()
            {
                getvalues();
            });

            google.maps.event.addListener(map, "click", function()
            {
                getvalues();
            });

            google.maps.event.addListener(map, "infowindowopen", function()
            {
                infowindowclosed = false;
            });

            google.maps.event.addListener(map, "infowindowclose", function()
            {
                infowindowclosed = true;
            });

            function showinfo(iwin) {
                //alert(i);
                iwin.open(map);
            }


            function getvalues()
            {
                var x1 = map.getBounds().getSouthWest().lat();
                var y1 = map.getBounds().getSouthWest().lng();
                var x2 = map.getBounds().getNorthEast().lat();
                var y2 = map.getBounds().getNorthEast().lng();

                var url = '/ajax/ajaxlisthouses/x1/' + x1 + '/y1/' + y1 + '/x2/' + x2 + '/y2/' + y2 ;

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
                            id = house.Id;

                            mytext = '<div class="labels"><br/><a href="/home/showinfo/field/description/houseid/'+id+'/">' + housename + '</a>';
                            mytext = mytext + '<br/>'+house.veryShortDescription+"</div>";

                            markeroptions = {
                                position:  new google.maps.LatLng(house.latitude,house.longitude),
                                draggable: false,
                                map: map,
                                title: "Maxpersons: "+maxpersons,
                                icon: 'http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld='+maxpersons+'|00ffff|000000'
                            }

                            iwin = (new google.maps.InfoWindow({
                                position: new google.maps.LatLng(house.latitude,house.longitude),
                                maxWidth: 600,
                                content: mytext
                            }));

                            marker = new google.maps.Marker(markeroptions);

                            google.maps.event.addListener(marker, "click", (function(iwin) {
                                return function() { showinfo(iwin);}})(iwin));
                        }
                    }
                });
            }
        }
    </script>
    <body onload="initialize()" onunload="" style="background-color:Transparent">
    <p class="header" id="message"></p>
    <p class="header" id="latlong"></p>
    <p class="header" id="ajaxmessage"></p>
    <div id="map_canvas" style="width: 100%; height: 650px"></div>
@endsection