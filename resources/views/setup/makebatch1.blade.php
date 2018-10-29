
@extends('layouts.app')
@section('content')

    <h3>{{ __('Create batchtask') }}</h3>
    <p>{{ __('For each house, certain rules exist. These are expressed in so-called batchtasks. When a new house has been added to the system, you should make batchtasks for this house, and this form allow you to copy a set of standard batchtasks to your house. To do so, press the Copy batch tasks.') }}</p>
    <div class="table-responsive table-sm">
        <table class="table table-striped">
            @foreach($houses as $house)
                <tr>
                    <th>
                        {{ $house->name }}
                    </th>
                    <td>
                        {{($batchexistss[$house->id] == 1)?__('Workflow made'):__('Workflow not made') }}
                    </td>
                    <td>
                        <a href="/setup/copybatch/{{$house->id}}/1/{{$batchexistss[$house->id]}}" class="btn btn-primary" role="button">{{($batchexistss[$house->id] == 1)?__('Overwrite batchjobs'):__('Copy batch')}}</a>
                    </td>
                    <td>
                        <a href="/setup/copybatch/{{$house->id}}/0/{{$batchexistss[$house->id]}}" class="btn btn-primary" role="button">{{($batchexistss[$house->id] == 1)?__('Copy missing batchjobs'):__('Copy batch')}}</a>
                    </td>
                </tr>
            @endforeach
        </table>
    </div>
    {!! $houses->appends(\Request::except('page'))->render() !!}
@endsection
