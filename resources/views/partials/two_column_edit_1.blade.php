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
            @if($vattr->getCast($field) != 'hidden')
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
                        @elseif($vattr->getCast($field) == 'bool')
                            {!! Form::checkbox($field, 1, ($model->$field == 1), $vattr->validationOptions($field, ['class' => 'form-control col-md-1'])) !!}
                        @else
                            {!! Form::text($field, $model->$field, $vattr->validationOptions($field, ['class' => 'form-control col-md-11', 'style' =>"height: 28px"])) !!}
                        @endif
                    </td>
                </tr>
            @endif
        @endforeach
    </table>
        @foreach($fields as $field)
            @if($vattr->getCast($field) == 'hidden')
                {!! Form::hidden($field, $model->$field, $vattr->validationOptions($field,[])) !!}
            @endif
        @endforeach
</div>
