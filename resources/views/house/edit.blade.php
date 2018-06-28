
@extends('layouts.app')
@section('content')

    <h3>Edit house</h3>
    <div class="table-responsive">
        {!! Form::model($model, ['action' => ['HouseController@update', $model], 'id' => 'HouseEdit']) !!}
        {!! Form::submit('Save changes'); !!}
        <?php $vattr = new ValidationAttributes();
            $vattr->setModel($model);
        ?>
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
            <div class="form-group row">
                @if($errors->first($field))
                    <div class="alert alert-danger">
                        <ul>
                            <li>{{ $errors->first($field) }}</li>
                        </ul>
                    </div>
                @endif
                {!! Form::label($field, ucfirst($field).':', ['class' => 'control-label col-md-4 col']) !!}
                 @if($field == 'latitude')
                    <input class="form-control col-md-6 col" data-rule-number="true" type="text" data-val="true" data-val-number="The field must be a number." data-val-range="Latitude must be between -180 and 180" data-val-range-max="180" data-val-range-min="-180" data-val-required="The latitude field is required." id="latitude" name="latitude" value="{{$model->$field}}" />

                 @endif
                @if($field != 'latitude')
                     {!! Form::text($field, $model->$field, $vattr->validationOptions($field, ['class' => 'col-md-6 col form-control'])) !!}
                @endif
                    <div class="text-danger col-md-8 field-validation-valid" data-valmsg-for="{{$field}}" data-valmsg-replace="true"></div>

            </div>


        @endforeach
        {!! Form::submit('Save changes'); !!}
        {!! Form::close() !!}
    </div>
@include('partials.client_validation')
@endsection