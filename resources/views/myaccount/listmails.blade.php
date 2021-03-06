@extends('layouts.app')
@section('content')
    <h3>{{ $title }}</h3>
    <div class="table-responsive">
        <table class="table table-striped">
            @if(sizeof($models) > 0)
                @foreach($models as $model)
                    <?php
                        $rentalinfo = '';
                        if ($model->contractid != 0)
                        {
                            $rentalinfo = $model->contract->house->name.__('for the period').': '.$model->contract->getPeriodtext();
                        }
                    ?>
                    <thead>
                        <tr>
                            <td class="border border-dark ">
                                <strong>{{ $model->created_at }}. {{__('From')}}: {{$model->from}} {{($rentalinfo != '')?__('regarding rental of').' '.$rentalinfo:''}}</strong>
                            </td>
                        </tr>
                    </thead>
                    <tr>
                        <td class="">
                            {!! $model->text !!}
                        </td>
                    </tr>
                @endforeach
            @else
                    <tr><td>{{__('No emails found.')}}</td></tr>
            @endif
            </tbody>
        </table>

    </div>
@endsection