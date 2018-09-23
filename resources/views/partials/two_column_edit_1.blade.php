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
        @foreach($fields as $field)
            @if($errors->first($field))
                <tr>
                    <td colspan="2">
                        <div class="alert alert-danger row">
                            {{ $errors->first($field) }}
                        </div>

                    </td>
                </tr>
            @endif
            <tr>
                <td>
                    {!! Form::label($field, __(ucfirst($field)).':', ['class' => 'col-md-11']) !!}
                </td>
                <td>
                    <div class="text-danger col-md-8 field-validation-valid row" data-valmsg-for="{{$field}}" data-valmsg-replace="true"></div>
                    @if($model->withSelect($field))
                        {!! Form::select($field,$model->withSelect($field),$model->$field,['class' => 'form-control col-md-11', 'style' => 'padding: 1px 0 3px 10px;']) !!}
                    @elseif($vattr->getCast($field) == 'textarea')
                        {!! Form::textarea($field, $model->$field, $vattr->validationOptions($field, ['class' => 'form-control col-md-11'])) !!}
                    @else
                        {!! Form::text($field, $model->$field, $vattr->validationOptions($field, ['class' => 'form-control col-md-11', 'style' =>"height: 28px"])) !!}
                    @endif
                </td>
            </tr>
        @endforeach
    </table>
</div>
