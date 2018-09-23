@extends('layouts.app')
@section('content')
    <h3>{{__('My bookings')}}</h3>
    <div class="table-responsive">
        <table class="table table-striped">
            @foreach($models as $model)
                <tr>
                    <td>
                        <strong>{{ $model->contract->house->name }}:</strong>
                    </td>
                </tr>
                <tr>
                    <td>{{__('Period')}}: {{ $model->contract->getPeriodtext(\App::getLocale()) }}. {{__('Final price')}}; {{ $model->contract->finalprice }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>

    </div>
@endsection