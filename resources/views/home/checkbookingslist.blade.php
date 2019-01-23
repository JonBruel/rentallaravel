@extends('layouts.app')
@section('content')
    <style>
        .occupied {
            background: #FF9191;
            margin-left: 15px;
        }
        .family {
            background: #00cc99;
            margin-left: 15px;
        }
        .notoffered {
            background: #005588;
            margin-left: 15px;
        }
        .halfday {
            background: #88ff66;
            margin-left: 15px;
        }
    </style>
    <h3>{{$house->name}} {{$house->address1}}</h3>
    <p>{{__('The bookings from now on and 52 weeks ahead.') . ' ' . __('Pink weeks are pending, red are occupied.') . ' ' . __('Turquoise weeks are for private use.')}}</p>
    <a class="btn btn-success col-md-12" href="/home/checkbookings?menupoint=10020&listtype=calendar">{{__('Change to calendar view')}}</a>
    <div class="table-responsive">
        <table class="table table-sm">
            <thead>
            <tr>
                <td>
                    {{__('Week')}}
                </td>
                <td>
                    {{__('Period')}}
                </td>
                <td>
                    {{__('Price')}}
                </td>
                <td>
                    {{__('Book it!') }}
                </td>
            </tr>
            </thead>
            <tbody>
            @foreach($models as $model)
                <?php
                $booktext = __('Press to book');
                $class = '';
                 $link = '<a class="btn btn-primary btn-sm" href="/contract/contractedit/0/'.$model->id.'">'.$booktext.'</a>';

                if ($model->committed)
                {
                    $class = 'occupied';
                    $booktext = __('Reserved');
                    if ($model->contract->categoryid == 1)
                    {
                        $class = 'family';
                        $booktext = __('Private');
                    }
                    $link = '<div align="center">' . $booktext . '<div>';
                }


                $priceinfo = 0;
                $r = $model->getRate(App::getLocale());
                $customercurrencysymbol = $r['currencysymbol'];
                $rate = $r['rate'];
                if ($model->personprice > 0)
                {
                    $priceinfo = __('Base price') . ': ' . $customercurrencysymbol . ' ' . Number::format($rate*$model->baseprice,['minimum_fraction_digits' => 2, 'maximum_fraction_digits' => 2, 'locale' => App::getLocale()]) . ', '
                        . __('per person more than') . ' ' . $model->basepersons . ': ' . $customercurrencysymbol . ' ' . Number::format($rate*$model->personprice,['minimum_fraction_digits' => 2, 'maximum_fraction_digits' => 2, 'locale' => App::getLocale()])
                        .  ', max. ' . $model->maxpersons;
                }
                else
                {
                    $priceinfo =  '&nbsp;' . $customercurrencysymbol . ' ' . Number::format($rate*$model->baseprice,['minimum_fraction_digits' => 2, 'maximum_fraction_digits' => 2, 'locale' => App::getLocale()])
                        . ' ' . __('with a maximum of') . ' ' . $model->maxpersons . ' ' . __('persons');
                }
                ?>
                <tr class="{{$class}}">
                    <td class="align-middle">
                       {{ $model->from->weekOfYear  }}
                    </td >
                    <td class="align-middle">
                        {{ $model->getEnddays() }}
                    </td>
                    <td class="align-middle">
                        {{ $priceinfo }}
                    </td>
                    <td class="align-middle">
                        {!! $link !!}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        {!! $models->appends(\Request::except('page'))->render() !!}
    </div>
@endsection
