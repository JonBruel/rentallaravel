@extends('layouts.app')
@section('content')
    <h3>{{ __('Batchlog') }}</h3>
    <br />
    <br /><br />
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
            <tr>
                    <td>
                    </td>
                    @foreach($fields as $field)
                        <td>{{ __($field) }}</td>
                    @endforeach
            </tr>
            <tr>
                <form id="Filter" action="{{Session::get('sanitizedpath')}}" method="get">
                    <td>
                        <button type="submit" class="btn btn-success glyphicon glyphicon-search" name="Search" value="Search"></button>
                    </td>
                    @foreach($fields as $field)
                        <td>
                            @if($model->withSelect($field))
                                {!! Form::select($field,$model->withSelect($field),(array_key_exists($field, $search))?$search[$field]:'',['class' => 'form-control col-md-11', 'style' => 'padding: 1px 0 3px 10px;']) !!}
                            @else
                                {!! Form::text($field,(array_key_exists($field, $search))?$search[$field]:'',null,['class' => 'form-control']) !!}
                            @endif
                        </td>
                    @endforeach
                    <td>
                </form>
            </tr>
            </thead>
            <tbody>
            @foreach($models as $model)
                <tr>
                    <td>
                        @include('partials.edit_delete', ['path' => 'batchlog', 'id' => $model->id])
                    </td>
                    @foreach($fields as $field)
                        <td>{{ $model->withBelongsTo($field) }}</td>
                    @endforeach
                </tr>
            @endforeach
            </tbody>
        </table>
        {!! $models->appends(\Request::except('page'))->render() !!}
    </div>

@endsection