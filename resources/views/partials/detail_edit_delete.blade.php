<?php
 if (!isset($params)) $params = '?'.session('querystring');
 $params = str_replace('page=', 'previouspage =', $params);
?>
<form class="delete" action="/{{$path}}/destroy/{{ $id }}" method="POST" id="delete{{ $id }}">
    <a href="/{{$path}}/show/{{ $id }}{{ $params }}" title="{{__('Show details')}}" data-toggle="tooltip"><span class='glyphicon glyphicon-list'></span></a>
    <a href="/{{$path}}/edit/{{ $id }}{{ $params }}" title="{{__('Edit')}}" data-toggle="tooltip"><span class='glyphicon glyphicon-pencil'></span></a>
    <input type="hidden" name="_method" value="DELETE">
    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
    <a href="#" id="btn-confirm{{ $id }}"  title="{{__('Delete')}}" data-toggle="tooltip"><span class='glyphicon glyphicon-remove' onclick='//$("#delete{{ $id }}").submit();return false;'></span></a>

</form>
<div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" id="my-modal{{ $id }}">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">{{ __('Do yoy really want to delete?') }}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span class='glyphicon glyphicon-remove' aria-hidden="true"></span></button>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" id="modal-btn-yes{{ $id }}">{{ __('Yes') }}</button>
                <button type="button" class="btn btn-primary" id="modal-btn-no{{ $id }}">{{ __('No') }}</button>
            </div>
        </div>
    </div>
</div>
<script>

    var modalConfirm = function(callback){

        $("#btn-confirm{{ $id }}").on("click", function(){
            $("#my-modal{{ $id }}").modal('show');
        });

        $("#modal-btn-yes{{ $id }}").on("click", function(){
            callback(true);
            $("#my-modal{{ $id }}").modal('hide');

        });

        $("#modal-btn-no{{ $id }}").on("click", function(){
            callback(false);
            $("#my-modal{{ $id }}").modal('hide');
        });
    };

    modalConfirm(function(confirm){
        if(confirm){
            $("#delete{{ $id }}").submit();
        }
    });

</script>

