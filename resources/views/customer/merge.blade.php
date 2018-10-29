@extends('layouts.app')
@section('content')
    <h3>{{ __('Merge customer') }}</h3>
    <div class="alert alert-warning border border-primary">{{ __('merge.customer.explain') }}</div>
    <br /><br />
    <div class="table-responsive table-sm">
        <table class="table table-striped">
            <thead>
            <tr>
                <form id="Filter" action="{{Session::get('sanitizedpath')}}" method="get">

                    <td>
                        <strong>{{__('Move this')}}</strong>
                    </td>
                    <td>
                        {!! Form::text('input1[name]',(array_key_exists('name', $input1))?$input1['name']:'',['id' => 'name', 'class' => 'form-control', 'onChange' => 'this.form.submit();', 'onfocus' => 'changeField(this, fldChanged);']) !!}
                    </td>
                    <td>
                        {!! Form::text('input1[email]',(array_key_exists('email', $input1))?$input1['email']:'',['class' => 'form-control', 'onChange' => 'this.form.submit();', 'onfocus' => 'changeField(this, fldChanged);']) !!}
                    </td>

            </tr>
            </thead>
            <tbody>
            @foreach($customers1 as $model)
                <tr>
                    <td>
                        {!! Form::radio('input1[from]', $model->id, (array_key_exists('from', $input1))?($input1['from'] == $model->id):false) !!}
                    </td>
                    <td>{{ $model->name }}</td>
                    <td>{{ $model->email }}</td>
                </tr>
            @endforeach
            </tbody>

            <thead>
            <tr>

                    <td>
                        <strong>{{__('To this')}}</strong>
                    </td>
                    <td>
                        {!! Form::text('input2[name]',(array_key_exists('name', $input2))?$input2['name']:'',['id' => 'name', 'class' => 'form-control', 'onChange' => 'this.form.submit();', 'onfocus' => 'changeField(this, fldChanged);']) !!}
                    </td>
                    <td>
                        {!! Form::text('input2[email]',(array_key_exists('email', $input2))?$input2['email']:'',['class' => 'form-control', 'onChange' => 'this.form.submit();', 'onfocus' => 'changeField(this, fldChanged);']) !!}
                    </td>

            </tr>
            </thead>
            <tbody>
            @foreach($customers2 as $model)
                <tr>
                    <td>
                        {!! Form::radio('input2[to]', $model->id, false) !!}
                    </td>
                    <td>{{ $model->name }}</td>
                    <td>{{ $model->email }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="3">
                    <button type="submit" class="btn btn-primary" name="action" value ="merge">{{ __('Merge chosen customers') }}</button>
                    </form>
                </td>
            </tr>
            </tbody>

        </table>
        {!! $customers1->appends(\Request::except('page'))->render() !!}
    </div>

@endsection
@section('scripts')
    <script type="text/javascript">
        function changeField(elm, after){
            var old, to, val,
                chk = function(){
                    val = elm.value;
                    if(!old && val === elm.defaultValue){
                        old = val;
                    }else if(old !== val){
                        old = val;
                        after(elm);
                    }
                };
            chk();
            to = setInterval(chk, 4000);
            elm.onblur = function(){
                to && clearInterval(to);
                elm.onblur = null;
            };
        };
        function fldChanged(elm){
            console.log('changed to:' + elm.value);
            $('#Filter').submit();
        }
    </script>
@endsection