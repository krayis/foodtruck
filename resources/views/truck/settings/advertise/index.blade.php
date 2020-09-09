@include('truck.layouts.admin.head')
@include('truck.layouts.admin.nav')
@include('truck.settings._partials.subnav')

<div class="container">
    <div class="page-header">
        <h1 class="mt-0">Advertise</h1>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <label for="">Your unique food truck id</label>
                <input type="text" class="form-control" value="{{ $user->truck->id }}" readonly />
            </div>
            <div class="form-group">
                <label for="">Your sharable calendar url</label>
                <input type="text" class="form-control" readonly />
            </div>
            <div class="form-group">
                <label for="">Your sharable order url</label>
                <input type="text" class="form-control"readonly />
            </div>
        </div>
    </div>
</div>
@include('truck.layouts.admin.footer')
