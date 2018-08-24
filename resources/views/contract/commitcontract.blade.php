@extends('layouts.app')
<?php
use Carbon\Carbon;
use Propaganistas\LaravelIntl\Facades\Number;
use Illuminate\Support\Facades\App;
?>
@section('content')
    <div class="table-responsive">
        <table class="table table-striped">
            <tr>
                <th>
                    <button class="btn btn-success" onclick="cmdPrint('#printerContent');return false;">{{ __('Print this order') }}</button>
                    <p>
                        {{ __('Thank you! The order will be E-mailed shortly using the E-mailaddress you have given us.') }}
                    </p>
                </th>
            </tr>
            <tr>
                <td>
                    <div id="printerContent">
                        {{ $mailtext }}
                    </div>
                </td>
            </tr>
        </table>
    </div>
@endsection
@section('scripts')
    <script type="text/javascript">
        function cmdPrint(element)
        {
            element = $(element);
/*
            var windowsettings = "tollbar=yes,location=no,scrollbar=yes,width=600,height=500,left=100,top=50";
            var popupcontroler = window.open('', '', windowsettings);
            popupcontroler.document.open();
            var stylesheettext = '<link rel="stylesheet" type="text/css" media="print,screen" href="/css/app.css" />';
            var title = '{{ __("Order confirmation")}}';
            popupcontroler.document.write('<html><head>'+stylesheettext+'<title>'+title+'</title>');
            popupcontroler.document.write('</title><body onload="self.print()"><div align=”center”>');
            popupcontroler.document.write(element.innerHTML);
            popupcontroler.document.write('</div></body></html>');
            popupcontroler.document.close();
 */
        }
    </script>
@endsection
