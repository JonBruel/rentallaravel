@extends('layouts.app')
@section('content')
    <h3>{{ __('Customers') }}</h3>
    <a href='/customer/create'>
        <span class='glyphicon glyphicon-plus'></span>
    </a>
    <br />
    <div class="table-responsive table-sm" style="max-width:100%">
        <table class="table table-striped"  style="max-width:99%; margin-left:2px">
            <thead>
            <tr>
                <th colspan="2">{{__('Customer type')}}</th>
                <th>@sortablelink('name')</th>
                <th>@sortablelink('address1')</th>
                <th>@sortablelink('email')</th>
            </tr>
            <tr  style="border-style: solid solid solid solid; border-width:4px 4px 4px 4px; border-color:green;">
                <form id="Filter" action="{{Session::get('sanitizedpath')}}" method="get">
                    <td colspan="2">
                        {!! Form::select('customertypeid',$customertypeselect,array_key_exists('customertypeid',
                        $search)?$search['customertypeid']:'',['class' => 'form-control col-md-11', 'style' => 'padding: 1px 0 3px 10px;', 'onChange' => 'this.form.submit();'])  !!}

                        <input type="hidden" name="sort" value="{{(array_key_exists('sort', $search))?$search['sort']:''}}" />
                        <input type="hidden" name="order" value="{{(array_key_exists('order', $search))?$search['order']:''}}" />
                    </td>
                    <td>
                        {!! Form::text('name',(array_key_exists('name', $search))?$search['name']:'',['id' => 'name', 'class' => 'form-control', 'onChange' => 'this.form.submit();', 'onfocus' => 'changeField(this, fldChanged);']) !!}
                    </td>
                    <td>
                        {!! Form::text('address1',(array_key_exists('address1', $search))?$search['address1']:'',['class' => 'form-control', 'onChange' => 'this.form.submit();', 'onfocus' => 'changeField(this, fldChanged);']) !!}
                    </td>
                    <td>
                        {!! Form::text('email',(array_key_exists('email', $search))?$search['email']:'',['class' => 'form-control', 'onChange' => 'this.form.submit();', 'onfocus' => 'changeField(this, fldChanged);']) !!}
                    </td>
                </form>
            </tr>
            </thead>
            <tbody>
            @foreach($models as $model)
                <tr>
                    <td>
                        @if($allowdeletes[$model->id])
                            @include('partials.detail_edit_delete', ['path' => 'customer', 'id' => $model->id])
                        @else
                            @include('partials.detail_edit', ['path' => 'customer', 'id' => $model->id])
                        @endif
                    </td>
                    <td>
                        <a href="/contract/listmails/{{ $model->id }}"  title="{{__('Check mails send from system').': '}} {{$model->name}}"  data-toggle="tooltip"><span class='glyphicon glyphicon-envelope'></span></a>
                        <a href="/customer/checkaccount/{{ $model->id }}"  title="{{__('Account posts').': '}} {{$model->name}}"  data-toggle="tooltip"><span class='glyphicon glyphicon-euro'></span></a>
                        <a href="/customer/merge/{{ $model->id }}"  title="{{__('Move customer accounts to the present user on the system').': '}} {{$model->name}}"  data-toggle="tooltip"><span class='glyphicon glyphicon-transfer'></span></a>
                        <a href="/impersonate/take/{{ $model->id }}"  title="{{__('See site as').': '}} {{$model->name}}"  data-toggle="tooltip"><span class='glyphicon glyphicon-user'></span></a>
                    </td>
                    <td>{{ $model->name }}</td>
                    <td>{{ $model->address1 }}</td>
                    <td>{{ $model->email }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        {!! $models->appends(\Request::except('page'))->render() !!}
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
            to = setInterval(chk, 2000);
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