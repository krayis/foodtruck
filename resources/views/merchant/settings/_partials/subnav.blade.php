<ul class="nav nav-tabs nav-tabs--darker">
    <li role="presentation"
        class="{{strpos(request()->route()->getAction()['as'], 'merchant.settings.index') !== false ? 'active' : null }}">
        <a href="{{ route('merchant.settings.index') }}">General</a></li>
    <li role="presentation"
        class="{{strpos(request()->route()->getAction()['as'], 'merchant.settings.payments') !== false ? 'active' : null }}">
        <a href="{{ route('merchant.settings.payments.index') }}">Payments</a></li>
    <li role="presentation"
        class="{{strpos(request()->route()->getAction()['as'], 'merchant.settings.alerts') !== false ? 'active' : null }}">
        <a href="{{ route('merchant.settings.alerts.index') }}">Alerts</a></li>
    <li role="presentation"
        class="{{strpos(request()->route()->getAction()['as'], 'merchant.settings.coupons') !== false ? 'active' : null }}">
        <a href="{{ route('merchant.settings.coupons.index') }}">Coupons</a></li>
    <li role="presentation"
        class="{{strpos(request()->route()->getAction()['as'], 'merchant.settings.advertise') !== false ? 'active' : null }}">
        <a href="{{ route('merchant.settings.advertise.index') }}">Advertise</a></li>
    <li role="presentation"
        class="{{strpos(request()->route()->getAction()['as'], 'merchant.settings.throttle') !== false ? 'active' : null }}">
        <a href="{{ route('merchant.settings.throttle.index') }}">Throttling</a></li>
</ul>

@foreach (['danger', 'warning', 'success', 'info'] as $message)
    @if(Session::has($message))
        <p class="alert alert-{{ $message }}">{{ Session::get($message) }}</p>
    @endif
@endforeach
