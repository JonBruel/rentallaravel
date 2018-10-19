
@extends('layouts.app')
@section('content')
    {!! Form::open(['url' => 'setup/updatecaptions/'.$id]) !!}
    {!! Form::submit(__('Save changes'),['class' => "btn btn-primary"]); !!}
    <br /><br />
    <div class="table-responsive">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <table class="table table-striped">
            <tr>
                <td style="width: 40px">
                    {{__('Edit').'/'.__('delete')}}
                </td>
                <td>
                    {{__('Key')}}
                </td>
                @foreach($cultures as $culture)
                <td>
                    {{$culturenames[$culture]}}
                </td>
                @endforeach
            </tr>
            <tr>
                <td>
                    {!! Form::checkbox('key[jkuuasg7892g]', 'jkuuasg7892g', 0, ['class' => 'form-control col-md-6']) !!}
                </td>
                <td>
                    {!! Form::text('jkuuasg7892g', '', ['class' => 'form-control col-md-11', 'style' =>"height: 28px"]) !!}
                </td>
                @foreach($cultures as $culture)
                    <td>
                        {!! Form::text('translation[jkuuasg7892g]['.$culture.']', '', ['class' => 'form-control col-md-11', 'style' =>"height: 28px"]) !!}
                    </td>
                @endforeach
            </tr>

            @foreach ($translationstartkey as $key => $translations)
                <tr>
                    <td>
                        <div class="row">
                            {!! Form::checkbox('key['.$key.']', $key, 0, ['class' => 'form-control col-md-6']) !!}
                            {!! Form::checkbox('delete['.$key.']', $key, 0, ['class' => 'form-control col-md-6']) !!}
                        </div>

                    </td>
                    <td>
                        {{substr($key,strlen($prefix))}}
                    </td>
                    @foreach($cultures as $culture)
                        <td>
                            {!! Form::text('translation['.$key.']['.$culture.']', $translations[$culture], ['class' => 'form-control col-md-11', 'style' =>"height: 28px"]) !!}
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </table>
    </div>
    {!! Form::close() !!}

@endsection
