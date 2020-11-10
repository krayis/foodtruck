@extends('truck.layouts.admin.layout')

@section('content')
    @foreach (['danger', 'warning', 'success', 'info'] as $message)
        @if(Session::has($message))
            <p class="alert alert-{{ $message }}">{{ Session::get($message) }}</p>
        @endif
    @endforeach

    <div class="page-header">
        <h1>Schedule</h1>
        <div class="page-action">
            <a class="btn btn-primary" href="{{ route('truck.schedule.create') }}">
                <ion-icon name="add"></ion-icon>
                New Date</a>
        </div>
    </div>


    <table class="table">
        <thead>
        <tr>
            <th>Date</th>
            <th>Start Time</th>
            <th>End Time</th>
            <th>Location</th>
            <th width="1%" class="white-space--nowrap"># of Pre-orders</th>
        </tr>
        </thead>
        <tbody>

        @if($events->count())
            @foreach($events as $event)
                <tr>
                    <td>
                        <a href="{{ route('truck.schedule.show', $event->id) }}">
                            {{ $event->startDatetime($timezone)->format('m/d/y') }}
                        </a>
                    </td>
                    <td>
                        {{ $event->startDatetime($timezone)->format('g:ia') }}
                    </td>
                    <td>
                        {{ $event->endDatetime($timezone)->format('g:ia') }}
                    </td>
                    <td>{!! strpos($event->location->formatted_address, $event->location->name) === false ?  $event->location->name . '<br/>' . $event->location->formatted_address: $event->location->formatted_address !!}</td>
                    <td align="center">0</td>
                </tr>
            @endforeach
        @else
            <tr class="warning">
                <td colspan="6" align="center">
                    You do not have any schedule dates.
                </td>
            </tr>
        @endif
        </tbody>
    </table>

@endsection
