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
        <h1>Add Location</h1>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="content">
                <form action="/truck/menu/location" method="POST">
                    <div class="form-group">
                        <label>Address</label>
                        <input type="text" class="form-control" />
                    </div>
                    <div class="form-group">
                        <div class="well">
                            <strong>Confirmed Address:</strong><br/>
                            10549 Creston Glen Cir E, Jacksonville, FL 32256
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="note">Date</label>
                        <input type="text" class="form-control">
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-12">
                                <label for="note">Begin Time</label>
                                <input type="text" class="form-control">
                            </div>
                            <div class="col-sm-12">
                                <label for="note">End Time</label>
                                <input type="text" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="note">Location Note</label>
                        <textarea class="form-control"></textarea>
                    </div>
                    <button class="btn btn-primary">Add Location</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key={{ config('app.google_api_key') }}&libraries=places"></script>
@include('truck.layouts.client.footer')
