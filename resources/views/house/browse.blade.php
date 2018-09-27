@extends('layouts.app')
@section('content')
    <h3>{{ __('Files used for presentation for').': '.$housename }}</h3>
        {!! Form::open(['url' => '/house/deletefiles/'.$id]) !!}
        <table class="tabularinfo">
            <tr>
                <th>
                    {!! Form::submit(__('Delete'),['class' => "btn btn-primary"]); !!}
                </th>
                <th>
                    {{ __('File name') }}
                </th>
            </tr>
            @foreach ($myfiles as $dir => $value)
                <tr>
                    <th colspan="2">
                     {{ ucfirst(__('directory')) . ': ' . __($dir) }}
                    </th>
                </tr>
                @foreach ($value as $name)
                    <tr class="">
                        <td class="">
                            {!! Form::checkbox('file[]', $dir.';'.$name, false, ['class' => 'col-md-12 col form-control', 'id' => 'file[]']) !!}
                        </td>
                        <td class="col-md-10">
                            @if($dir == 'Gallery')
                                <a href="{{'/housegraphics/'.$id.'/gallery1/'.$name}}">{{$name}}</a>
                            @else
                                <a href="{{'/'.strtolower(substr($dir,0,1)).substr($dir,1).'/'.$id.'/'.$name}}">{{$name}}</a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            @endforeach
        </table>
    {!! Form::close() !!}
@endsection