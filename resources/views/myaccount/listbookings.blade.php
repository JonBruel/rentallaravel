@extends('layouts.app')
@section('content')
    <h3>{{__('My bookings')}}</h3>
    <div class="table-responsive">
        <table class="table table-striped">
            @foreach($models as $model)
                <tr>
                    <td>
                        <strong>{{ $model->contract->house->name }}:</strong>
                        @if(Carbon\Carbon::now()->lt($model->contract->contractlines()->first()->period->to))
                            <a href="/contract/limitedcontractedit/{{$model->contract->id}}" class="btn btn-primary" role="button">{{ __('Change number of guests') }}</a>
                            <a href="/myaccount/editidentitypapers/{{$model->contract->id}}" class="btn btn-primary" role="button">{{ __('Guest passport details') }}</a>
                            {{ __('For other changes, please contact the owner.') }}
                        @endif
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
