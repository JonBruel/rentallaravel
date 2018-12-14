<style>
    .legend .row:nth-of-type(odd) div {
        background-color: #EEEEEE;
        padding: 6px 3px 6px 3px;
    }
    .legend .row:nth-of-type(even) div {
        background-color: #FFFFFF;
        padding: 6px 3px 6px 3px;
    }
</style>
<div class="row col-md-12 center-block">
    <div class="col col-md-3"></div>
    <div class="border border-primary rounded col-md-6" id="checkvacancies" style="border-width: 2px !important; margin-top: 10px; padding-left: 2px; padding-right: 2px; background: white;"></div>
</div>

@section('scripts')
    <script type="text/javascript">
        culture = "{{App::getLocale()}}";
        culture = culture.replace("_", "-");



        function order(id)
        {
            $('#checkvacancies').fadeOut();
            location.href = '/contract/contractedit/0/'+id;

        }

        $(document).ready(function(){
            getMonths({{$houseid}});
        });

        function highlight(name)
        {
            //Uncolor all others
            $.each(allmonths, function(key, val) {
                elm = $('#'+val);
                elm.css('background-color', '');
                elm.prop('reserved', 0);
            });

            element = $('#'+name);
            element.css('background-color', 'LightGreen');
            element.prop('reserved', '1');
        }


        /*Build ajax call to populate showweeks division
          We load the week in chuncks and save the chuncks in the
          global periodchunk array.
         */
        allmonths = [];
        function getMonths(houseid)
        {
            cont = false;
            culture = '{{App::getLocale()}}';

            url = '/ajax/getmonths/' + houseid;
            new $.getJSON(url, function(months)
            {
                vacantmonthnumber = 0;
                clickmessage = "{{__('Monthly vacancies')}}"+':';
                title = "{{__('Click to choose specific rental periods')}}"+'.';
                content =   '<div class="legend"  style="margin-left: 0; max-height: 230px; overflow-y: scroll;  overflow-x: hidden; padding-top: 3px; padding-bottom: 3px;">\n' +
                            '<div class="row">' +
                            '<div class="center-block col-md-12" style="text-align:center;">' +
                            '<span><strong>'+clickmessage+'</strong></span>' +
                            '</div>' +
                            '</div>';

                months.forEach(month => {
                    allmonths.push(month.month);
                    style = 'text-align:center;';
                    if (month.vacancies == 0)
                    {
                        style += ' opacity: 0.55';
                    }
                    om = '';
                    oc = '';
                    if (month.vacancies > 0)
                    {
                        vacantmonthnumber++;
                        //om += 'onmouseover="highlight(\''+month.month+'\');false;"';
                        oc += 'onclick="order('+month.id+');" dusk="vacantmonth' + vacantmonthnumber + '"';
                    }
                    content +=  '<div class="row" style="z-index: 1000">' +
                        '<div style="'+style+'" class="col-sm-12" '+om+' '+oc+' id="'+month.month+'" title="'+title+'" data-toggle="tooltip">' + month.text + '</div>' +
                        '</div>';
                });

                $('#checkvacancies').html(content);
                $(function () {
                    $('[data-toggle="tooltip"]').tooltip();
                })
            });
        }

    </script>
@endsection
