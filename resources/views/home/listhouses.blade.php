@extends('layouts.app')
@section('content')
    <h3>Choose house</h3>
    <div class="table-responsive">
        <table class="table table-striped">
            <tbody>
            @foreach($models as $model)
                <tr>
                    <td>
                       <a href="/home/listhouses?defaultHouse={{$model->id}}">{{$model->id}}</a>
                    </td>
                    <td>{{ $model->name }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        {!! $models->appends(\Request::except('page'))->render() !!}
    </div>
@endsection