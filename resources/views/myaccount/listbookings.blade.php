@extends('layouts.app')
@section('content')
    <h3>{{__('My bookings')}}</h3>
    <div class="table-responsive">
        <table class="table table-striped">
            @foreach($models as $model)
                <tr>
                    <td>
                        <strong>{{ $model->contract->house->name }}:</strong> {{ __('To change the order, please contact the owner.') }}
                    </td>
                </tr>
                <tr>
                    <td>{!! $model->contract->getOrder() !!} </td>
                </tr>
            @endforeach
            </tbody>
        </table>

    </div>
@endsection
