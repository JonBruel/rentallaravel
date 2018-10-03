@extends('layouts.app')
@section('content')
    <style>
        table.table-bordered{
            border:1px solid blue;
            margin-top:20px;
        }
        table.table-bordered > thead > tr > th{
            border:1px solid blue;
        }
        table.table-bordered > tbody > tr > td{
            border:1px solid blue;
        }
    </style>
    <h3>{{ __('Error log') }}</h3>
    <br /><br />
    <div class="table-responsive" style="max-width: 1200px">
        <table class="table table-bordered table-sm">
            <tbody>
            <tr>
                    <td>
                    </td>
                    <td>
                        {!! Form::label('created_at',__('created_at')) !!}
                    </td>
                    <td>
                        {!! Form::label('stack',__('stack')) !!}
                    <td>
                        {!! Form::label('situation',__('situation')) !!}
                    </td>
                    <td>
                        {!! Form::label('customermessage',__('customermessage')) !!}
                    </td>
                </form>
            </tr>
            <tr>
                <form id="Filter" action="{{Session::get('sanitizedpath')}}" method="get">
                    <td>
                        {!! Form::submit(__('Search'),['class' => 'form-control btn btn-primary']) !!}
                    </td>
                    <td>
                        {!! Form::text('created_at',(array_key_exists('created_at', $search))?$search['created_at']:'',null,['class' => 'form-control']) !!}
                    </td>
                    <td>
                        {!! Form::text('stack',(array_key_exists('stack', $search))?$search['stack']:'',null,['class' => 'form-control']) !!}
                    </td>
                    <td>
                        {!! Form::text('situation',(array_key_exists('situation', $search))?$search['situation']:'',null,['class' => 'form-control']) !!}
                    </td>
                    <td>
                        {!! Form::text('customermessage',(array_key_exists('customermessage', $search))?$search['customermessage']:'',null,['class' => 'form-control']) !!}
                    </td>
                </form>
            </tr>


            @foreach($models as $model)
                <tr class="border border-dark">
                    <td>
                        {{__('created_at')}}
                    </td>
                    <td colspan="4">
                        {{ $model->created_at }}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{__('situation')}}
                    </td>
                    <td colspan="4">
                        {!! $model->situation  !!}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{__('customermessage')}}
                    </td>
                    <td colspan="4">
                        {!! $model->customermessage  !!}
                    </td>
                </tr>
                <tr>
                    <td colspan="5">
                        <strong> Stack trace: </strong>
                    </td>
                </tr>
                <tr>
                    <td colspan="5">
                        {!! str_replace('#', '<br/>#', $model->stack) !!}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        {!! $models->appends(\Request::except('page'))->render() !!}
    </div>
@endsection
