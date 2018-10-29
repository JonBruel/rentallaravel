
@extends('layouts.app')
@section('content')

    <h3>{{ __('Please confirm') }}</h3>
     <div class="table-responsive table-sm">
        <table class="table table-striped">
                <tr>
                    <td>
                        <a href="/setup/copybatch/{{$houseid}}/{{$overwrite}}/{{$batchexists}}?answer=yes" class="btn btn-primary btn-lg" role="button">{{ __('Yes')}}</a>
                    </td>
                    <td>
                        <a href="/setup/copybatch/{{$houseid}}/{{$overwrite}}/{{$batchexists}}?answer=no" class="btn btn-primary btn-lg" role="button">{{ __('No')}}</a>
                    </td>
                </tr>
        </table>
    </div>
@endsection
