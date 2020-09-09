@include('truck.layouts.admin.head')
@include('truck.layouts.admin.nav')
@include('truck.settings._partials.subnav')

<div class="container">
    <div class="page-header">
        <h1 class="mt-0">Alert Notifications</h1>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <p>You will copied on all customers emails as orders are received.</p>
            <form action="{{ route('truck.settings.alerts.update', $user->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="form-group">
                    <label for="">SMS notifications</label>
                    <input type="text" name="mobile_phone" class="form-control" value="{{ $user->mobile_phone }}">
                </div>
                <div class="form-group">
                    <label for="">Email notifications</label>
                    <input type="text" name="email_notification" class="form-control" value="{{ $user->email_notification ? $user->email_notification : $user->email }}">
                </div>
                <button class="btn btn-primary">Save</button>
            </form>
        </div>
    </div>
</div>

@include('truck.layouts.admin.footer')
