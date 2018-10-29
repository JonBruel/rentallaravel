@extends('layouts.app')
@section('content')
    <div class="table-responsive table-sm)">
        <div style="margin-top: 30px">
            {!! __('gdpr') !!}
        </div>
    </div>
    <script>
        $("#checkvacanciesappear").fadeIn(5000);
        //$('#vacancyPrMonth').draggable;
    </script>
@endsection
