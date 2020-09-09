@include('truck.layouts.admin.head')
@include('truck.layouts.admin.nav')
<div class="container">
    <div class="flash-message">
        @foreach (['danger', 'warning', 'success', 'info'] as $message)
            @if(Session::has($message))
                <p class="alert alert-{{ $message }}">{{ Session::get($message) }}</p>
            @endif
        @endforeach
    </div>
</div>
<div class="container">
    <div class="page-header">
        <h1>Schedule</h1>
        <div class="page-action">
            <a class="btn btn-primary" href="{{ route('truck.schedule.create') }}">Add Date</a>
        </div>
    </div>

    <div class="content">
        <table class="table">
            <thead>
            <tr>
                <th>Date</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Location</th>
                <th width="1%" class="white-space--nowrap"># of Pre-orders</th>
                <th width="1%"></th>
            </tr>
            </thead>
            <tbody>

            @if($events->count())
                @foreach($events as $event)
                    <tr>
                        <td>
                            {{ Date('m/d/y', strtotime($event->start_date_time)) }}
                        </td>
                        <td>
                            {{ Date('h:i a', strtotime($event->start_date_time)) }}
                        </td>
                        <td>
                            {{ Date('h:i a', strtotime($event->end_date_time)) }}
                        </td>
                        <td>{!! strpos($event->location->formatted_address, $event->location->name) === false ?  $event->location->name . '<br/>' . $event->location->formatted_address: $event->location->formatted_address !!}</td>
                        <td align="center">0</td>
                        <td>
                            <form action="{{ route('truck.schedule.destroy', $event->id) }}" style="display: none;" method="POST">
                                @csrf
                                @method('DELETE')
                            </form>
                            <div class="dropdown">
                                <a href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                    <ion-icon name="ios-more"></ion-icon>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenu1">
                                    <li><a href="{{ route('truck.schedule.show', $event->id) }}"><ion-icon name="ios-create"></ion-icon> Edit</a></li>
                                    <li><a href="#" class="js-table-delete"><ion-icon name="ios-trash"></ion-icon> Delete</a></li>
                                </ul>
                            </div>
                        </td>
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
    </div>
</div>
<script>
    $('.js-table-delete').on('click', function (e) {
        e.preventDefault();
        if (confirm('Are you sure you want to delete?')) {
            $(this).closest('td').find('form').submit();
        }
    });
</script>
@include('truck.layouts.client.footer')
