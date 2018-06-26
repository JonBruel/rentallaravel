<script type="text/javascript" src="{{ asset('vendor/jquery/dist/jquery.validate.js', true)}}"></script>
<script type="text/javascript" src="{{ asset('vendor/jquery/dist/jquery.validate.unobtrusive.js', true)}}"></script>

<!-- cldr scripts (needed for globalize)-->
<script src="{{ asset('vendor/cldrjs/dist/cldr.js', true) }}"></script>
<script src="{{ asset('vendor/cldrjs/dist/cldr/event.js', true) }}"></script>
<script src="{{ asset('vendor/cldrjs/dist/cldr/supplemental.js', true) }}"></script>

<script type="text/javascript" src="{{ asset('vendor/globalize/js/globalize.js', true)}}"></script>
<script type="text/javascript" src="{{ asset('vendor/globalize/js/globalize/number.js', true)}}"></script>
<script type="text/javascript" src="{{ asset('vendor/globalize/js/globalize/date.js', true)}}"></script>

<script type="text/javascript" src="{{ asset('vendor/globalize/js/jquery.validate.globalize.js', true)}}"></script>
<script type="text/javascript">
    var culture = "{{App::getLocale()}}";
    if (culture == 'da') culture = 'da-DK';
    $.when(
        $.get("/vendor/cldr-data/supplemental/likelySubtags.json"),
        $.get("/vendor/cldr-data/main/" + culture + "/numbers.json"),
        $.get("/vendor/cldr-data/supplemental/numberingSystems.json"),
        $.get("/vendor/cldr-data/main/" + culture + "/ca-gregorian.json"),
        $.get("/vendor/cldr-data/main/" + culture +"/timeZoneNames.json"),
        $.get("/vendor/cldr-data/supplemental/timeData.json"),
        $.get("/vendor/cldr-data/supplemental/weekData.json")
    ).then(function () {
        // Normalize $.get results, we only need the JSON, not the request statuses.
        return [].slice.apply(arguments, [0]).map(function (result) {
            return result[0];
        });
    }).then(Globalize.load).then(function () {
        Globalize.locale(culture);
    });
</script>