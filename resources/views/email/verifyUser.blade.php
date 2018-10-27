@extends('layouts.email')
@section('content')
    <h2>Welcome to the site {{$user['name']}}</h2>
    <br/>
    Your registered email-id is {{$user['email']}} , Please click on the below link to verify your email account
    <br/>
    <a href="{{url('user/verify', $user->verifyUser->token)}}">Verify Email</a>
    <br/>
    {{__("When done, please go to 'My account' and enter your contact information.")}}
@endsection