@include('truck.layouts.admin.head')
@include('truck.layouts.admin.nav')
<div class="container">
    <div class="flash-message">
        @foreach (['danger', 'warning', 'success', 'info'] as $message)
            @if(Session::has($message))
                <div class="alert alert-{{ $message }}">{{ Session::get($message) }}</div>
            @endif
        @endforeach
    </div>
</div>
<div class="container">
    <div class="page-header">
        <h1>Locations</h1>
        <div class="page-action">
            <a class="btn btn-primary" href="{{ route('truck.location.create') }}">Add Location</a>
        </div>
    </div>
    <div class="content">
        <table class="table">
            <thead>
            <tr>
                <th>Address</th>
                <th>Note</th>
                <th width="1%"></th>
            </tr>
            </thead>
            <tbody>
            @foreach($locations as $location)
                <tr>
                    <td>{!! strpos($location->formatted_address, $location->name) === false ?  $location->name . '<br/>' . $location->formatted_address: $location->formatted_address !!}</td>
                    <td>{{ $location->note }}</td>
                    <td>
                        <form action="{{ route('truck.location.destroy', $location->id) }}" style="display: none;" method="POST">
                            @csrf
                            @method('DELETE')
                        </form>
                        <div class="dropdown">
                            <a href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                <ion-icon name="ios-more"></ion-icon>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenu1">
                                <li><a href="{{ route('truck.location.edit', $location->id) }}">Edit</a></li>
                                <li><a href="#" class="js-table-delete">Delete</a></li>
                            </ul>
                        </div>
                    </td>
                </tr>
            @endforeach
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
