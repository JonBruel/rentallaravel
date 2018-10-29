@extends('layouts.app')
@section('content')
    <h3>{{ __('What customers have said about')}} {{$house->name}}</h3>
    <div class="table-responsive table-sm">
        <table class="table table-striped">
            <tbody>
            @if(Auth::check())
                <tr>
                    <td>
                        {!! Form::open(['action' => 'HomeController@createtestimonial']) !!}
                        {!! Form::submit(__('Save changes'),['class' => "btn btn-primary"]); !!}
                        {{ __('Please review the place! When done, press the "Save" button') }}<br /><br />
                        {!! Form::textarea('text', '', ['class' => 'col-md-12 col form-control']) !!}
                        {!! Form::hidden('houseid', $houseid) !!}
                        {!! Form::close() !!}
                    </td>
                </tr>
            @endif
            @foreach($models as $model)
                <tr>
                    <td colspan="1">
                       {{ $model->created_at->format('d-m-Y') }}
                        <br /><br />
                        {{ $model->text }}
                        @if($administrator)
                            <br />
                            @include('partials.detail_edit_delete', ['path' => 'testimonial', 'id' => $model->id, 'params' => null])
                            <br />
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

    </div>
@endsection