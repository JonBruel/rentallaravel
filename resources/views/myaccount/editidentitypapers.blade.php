@extends('layouts.app')
@section('content')
    <h3>{{__('Trip passport details') . ', ' .  $contractdescription}}</h3>
    <p>{{ __('Identitypaper gdpr statement') }}</p>
    @if(sizeof($models) > 0)
        <!-- Table showing existing passport numbers -->
        <div class="table-responsive">
            <table class="table table-striped">
                <tr>
                    <th>
                        {{ __('Edit') }}
                    </th>
                    <th>
                        {{ ucfirst(__('Forename') )}}
                    </th>
                    <th>
                        {{ ucfirst(__('Surname1')) }}
                    </th>
                    <th>
                        {{ ucfirst(__('Passportnumber')) }}
                    </th>
                </tr>
                @foreach($models as $model)
                    <tr>
                        <td>
                            @include('partials.edit_delete', ['path' => 'myaccount', 'id' => $model->id])
                        </td>
                        <td>
                            {{ $model->forename }}
                        </td>
                        <td>
                            {{ $model->surname1 }}
                        </td>
                        <td>
                            {{ $model->passportnumber}}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
        <!-- Form for new passposr numbers -->
        <h3>{{__('Add guest passport details')}}</h3>
        <div class="table-responsive table-sm">
            {!! Form::model($newidentitypaper, ['action' => ['MyAccountController@saveidentitypaper', $newidentitypaper]]) !!}
            <br />
            <br />

            @include('partials.two_column_edit_1', ['model' => $newidentitypaper, 'errors' => $errors, 'fields' => $fields, 'vattr' => $vattr])
            {!! Form::submit(__('Add new identity paper'),['class' => "btn btn-primary"]); !!}
            {!! Form::close() !!}
        </div>
        @include('partials.client_validation')

    </div>
@endsection
@section('scripts')
    <link href="{{ asset('/vendor/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css', config('app.secure', false))}}" rel="stylesheet">
    <script type="text/javascript" src="{{ asset('/vendor/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js', config('app.secure', false))}}"></script>
    <script type="text/javascript">
        culture = "{{App::getLocale()}}";
        culture = culture.replace("_", "-");

        //$(document).ready(getWeeks(0));
        $(function() {
            $('#dateofissue').datetimepicker({ language: '{{str_replace('_', '-', App::getLocale())}}',
                format: 'dd-mm-yyyy',
                autoclose: true,
                minView: 2,
                maxView: 4,
                startView: 4});

            $('#dateofbirth').datetimepicker({ language: '{{str_replace('_', '-', App::getLocale())}}',
                format: 'dd-mm-yyyy',
                autoclose: true,
                minView: 2,
                maxView: 4,
                startView: 4});
        });
    </script>
@endsection

