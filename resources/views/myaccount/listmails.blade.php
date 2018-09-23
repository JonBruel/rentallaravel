@extends('layouts.app')
@section('content')
    <h3>{{__('Emails sent to me')}}</h3>
    <div class="table-responsive">
        <table class="table table-striped">
            @foreach($models as $model)
                <thead>
                    <tr>
                        <td class="border border-dark ">
                            <strong>{{ $model->created_at }}. {{__('From')}}: {{$model->from}}:</strong>
                        </td>
                    </tr>
                </thead>
                <tr>
                    <td class="">
                        {!! $model->text !!}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

    </div>
@endsection