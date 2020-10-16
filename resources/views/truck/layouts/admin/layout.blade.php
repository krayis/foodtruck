@include('truck.layouts.admin.head')

<div class="container-fluid">
    <div class="row">
        <div class="col-sm-5 col-md-4 sidebar">
            @include('truck.layouts.admin.nav')
        </div>
        <div class="col-sm-21 col-sm-offset-5 col-md-20 col-md-offset-4 main">
            @yield('content')
        </div>
    </div>
</div>

@include('truck.layouts.admin.footer')
