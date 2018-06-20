@extends('layouts.app');
@section('content')
    <h3>Houses</h3>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Address1</th>
            </tr>
            </thead>
            <tbody>
            @foreach($houses as $house)
                <tr>
                    <td><a href="/house/{{ $house->id }}">{{ $house->id }}</a></td>
                    <td>{{ $house->name }}</td>
                    <td>{{ $house->address1 }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection