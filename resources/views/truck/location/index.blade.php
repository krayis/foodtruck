@extends('truck.layouts.admin.layout')

@section('content')
    @foreach (['danger', 'warning', 'success', 'info'] as $message)
        @if(Session::has($message))
            <div class="alert alert-{{ $message }}">{{ Session::get($message) }}</div>
        @endif
    @endforeach

    <div class="page-header">
        <h1>Locations</h1>
        <div class="page-action">
            <a class="btn btn-primary" href="{{ route('truck.location.create') }}">
                <ion-icon name="add" role="img" class="md hydrated" aria-label="add"></ion-icon>
                New Location</a>
        </div>
    </div>

    <table class="table">
        <thead>
        <tr>
            <th>Address</th>
            <th>Note</th>
        </tr>
        </thead>
        <tbody>
        @foreach($locations as $location)
            <tr>
                <td>
                    <a href="{{ route('truck.location.edit', $location->id) }}">
                        {!! strpos($location->formatted_address, $location->name) === false ?  $location->name . '<br/>' . $location->formatted_address: $location->formatted_address !!}
                    </a>
                </td>
                <td>{{ $location->note }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $locations->links() }}
@endsection
