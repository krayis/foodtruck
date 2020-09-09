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
    <ul class="nav nav-tabs">
        <li role="presentation" class="{{strpos(request()->route()->getAction()['as'], 'truck.settings.index') !== false ? 'active' : null }}"><a href="{{ route('truck.settings.index') }}">General</a></li>
        <li role="presentation" class="{{strpos(request()->route()->getAction()['as'], 'truck.settings.payments') !== false ? 'active' : null }}"><a href="{{ route('truck.settings.payments.index') }}">Payments</a></li>
        <li role="presentation" class="{{strpos(request()->route()->getAction()['as'], 'truck.settings.alerts') !== false ? 'active' : null }}"><a href="{{ route('truck.settings.alerts.index') }}">Alerts</a></li>
        <li role="presentation" class="{{strpos(request()->route()->getAction()['as'], 'truck.settings.coupons') !== false ? 'active' : null }}"><a href="{{ route('truck.settings.coupons.index') }}">Coupons</a></li>
        <li role="presentation" class="{{strpos(request()->route()->getAction()['as'], 'truck.settings.advertise') !== false ? 'active' : null }}"><a href="{{ route('truck.settings.advertise.index') }}">Advertise</a></li>
        <li role="presentation" class="{{strpos(request()->route()->getAction()['as'], 'truck.settings.throttle') !== false ? 'active' : null }}"><a href="{{ route('truck.settings.throttle.index') }}">Throttling</a></li>
    </ul>
</div>
