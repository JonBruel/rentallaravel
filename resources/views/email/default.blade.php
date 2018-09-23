@extends('layouts.email')
@section('content')
 {!! $contents !!}
<br /><br />
{{$fromName}}
@endsection