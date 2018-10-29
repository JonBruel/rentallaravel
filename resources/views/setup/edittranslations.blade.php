
@extends('layouts.app')
@section('content')
    <h3>{{__('Translations')}}</h3>
    <br />
    {!! Form::open(['url' => 'setup/edittranslations']) !!}
    {!! Form::submit(__('Save changes'),['class' => "btn btn-primary", 'name' => 'Save']); !!}
    <br /><br />
    @php($seqnumber = 0)
    <div class="table-responsive  table-sm col-md-12">
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
                <td style="width: 20px">
                    {{__('Edit') }}
                </td>
                <td style="width: 20px">
                    {{__('Delete')}}
                </td>
                <td style="max-width: 33%">
                    {{__('Key')}}
                </td>
                @foreach($cultures as $culture)
                <td>
                    {{$culturenames[$culture]}}
                </td>
                @endforeach
            </tr>
            <tr  style="border-style: solid solid solid solid; border-width:4px 4px 4px 4px; border-color:green;">
                <td colspan="2">
                    <button type="submit" class="btn btn-success glyphicon glyphicon-search" name="Search" value="Search"></button>
                </td>
                <td>
                    {!! Form::text('searchkey', $searchkey, ['class' => 'form-control col-md-11', 'style' =>"height: 28px"]) !!}
                </td>
                @foreach($cultures as $culture)
                    <td>
                        {!! Form::text('text['.$culture.']', $textsearches[$culture], ['class' => 'form-control col-md-11', 'style' =>"height: 28px"]) !!}
                    </td>
                @endforeach
            </tr>
            <tr>
                <td colspan="3">
                    <strong>{{__('New translation')}}</strong>
                </td>
                @foreach($cultures as $culture)
                    <td>
                    </td>
                @endforeach
            </tr>

            <tr>
                <td>
                    {!! Form::checkbox('key[jkuuasg7892g]', 'jkuuasg7892g', 0, ['class' => 'form-control col-md-6', 'id' => 'edit'.$seqnumber]) !!}
                </td>
                <td></td>
                <td>
                    <span onclick="$('#jkuuasg7892g').toggle();$('#{{'edit'.$seqnumber}}').prop('checked', true);">{{__('Click to open text area.')}}</span>
                    {!! Form::textarea('jkuuasg7892g', '', ['class' => 'form-control col-md-11', 'style' =>"height: 28px", 'id' => 'jkuuasg7892g', 'style' =>'display: none;']) !!}
                </td>
                @foreach($cultures as $culture)
                    <td>
                        <span onclick="$('#{{'translation_'.$culture.$seqnumber}}').toggle();$('#{{'edit'.$seqnumber}}').prop('checked', true);">{{__('Click to open text area.')}}</span>
                        {!! Form::textarea('translation[jkuuasg7892g]['.$culture.']', '', ['class' => 'form-control col-md-11', 'style' =>'display: none;', 'id' => 'translation_'.$culture.$seqnumber]) !!}
                    </td>
                @endforeach
            </tr>
            <tr>
                <td colspan="3">
                    <strong>{{__('Existing translations')}}</strong>
                </td>
                @foreach($cultures as $culture)
                    <td>
                    </td>
                @endforeach
            </tr>
            @foreach ($translationstartkey as $key => $translations)
                @php($seqnumber++)
                <tr>
                    <td>
                        {!! Form::checkbox('key['.$key.']', $key, 0, ['class' => 'form-control col-md-6', 'id' => 'edit'.$seqnumber]) !!}

                    </td>
                    <td>
                        {!! Form::checkbox('delete['.$key.']', $key, 0, ['class' => 'form-control col-md-6']) !!}
                    </td>
                    <td>
                        <span onclick="$('#{{'keytext'.$seqnumber}}').toggle();$('#{{'edit'.$seqnumber}}').prop('checked', true);">{!! substr($key,0,50) !!}</span>
                        {!! Form::textarea('keytexts['.$key.']', $key, ['id' => 'keytext'.$seqnumber, 'class' => 'form-control col-md-11', 'style' =>'display: none;']) !!}

                    </td>
                    @foreach($cultures as $culture)
                        <td>
                            <span onclick="$('#{{'translation_'.$culture.$seqnumber}}').toggle();$('#{{'edit'.$seqnumber}}').prop('checked', true);">{{ substr((array_key_exists($culture, $translations))?$translations[$culture]:__('Translation missing'),0,50) }}</span>
                            {!! Form::textarea('translation['.$key.']['.$culture.']', (array_key_exists($culture, $translations))?$translations[$culture]:__('Translation missing'), ['id' => 'translation_'.$culture.$seqnumber, 'class' => 'form-control col-md-11', 'style' =>'display: none;']) !!}
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </table>
    </div>
    {!! Form::close() !!}

@endsection
