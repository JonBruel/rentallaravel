<div class="table-responsive table-sm">
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @foreach($fields as $field)
        @if($errors->first($field))
            <div class="form-group row">
                <div class="alert alert-danger row">
                    {{ $errors->first($field) }}
                </div>
            </div>
        @endif
        @if($vattr->getCast($field) != 'hidden')
            <div class="form-group row">
                {!! Form::label($field, __(ucfirst($field)).':', ['class' => 'col-md-3']) !!}
                <div class="col-md-9">
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
                </div>
            </div>
        @endif
    @endforeach

    @foreach($fields as $field)
        @if($vattr->getCast($field) == 'hidden')
            {!! Form::hidden($field, $model->$field, $vattr->validationOptions($field,[])) !!}
        @endif
    @endforeach
</div>
