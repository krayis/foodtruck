<ul class="nav nav-sidebar">
    <li class="nav-brand">
        Foodmerchant.am
    </li>
    <li class="{{strpos(URL::current(),'/merchant/orders') !== false ? "active" : null }}"><a
            href="{{ route('merchant.orders.index') }}">
            <ion-icon name="laptop"></ion-icon>
            Orders</a></li>
    <li class="{{strpos(URL::current(),'/merchant/status' ) ? "active" : null}}"><a
            href="{{ route('merchant.status.index') }}">
            <ion-icon name="time"></ion-icon>
            My Status</a></li>
    <li class="{{strpos(URL::current(),'/merchant/menu' ) ? "active" : null}}"><a
            href="{{ route('merchant.menu.index') }}">
            <ion-icon name="document"></ion-icon>
            Menu</a></li>
    <li class="{{strpos(URL::current(),'/merchant/inventory' ) ? "active" : null}}"><a
            href="{{ route('merchant.inventory.templates.index') }}">
            <ion-icon name="cube"></ion-icon>
            Inventory</a></li>
    <li class="{{strpos(URL::current(),'/merchant/schedule') ? "active" : null}}"><a
            href="{{ route('merchant.schedule.index') }}">
            <ion-icon name="calendar"></ion-icon>
            Schedule</a></li>
    <li class="{{strpos(URL::current(),'/merchant/location') ? "active" : null}}"><a
            href="{{ route('merchant.location.index') }}">
            <ion-icon name="pin"></ion-icon>
            Locations</a></li>
    <li class="{{strpos(URL::current(),'/merchant/settings') ? "active" : null}}"><a
            href="{{ route('merchant.settings.index') }}">
            <ion-icon name="cog"></ion-icon>
            Settings</a></li>
    <li>
        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <ion-icon name="log-out"></ion-icon>
            Logout
        </a>
        <form id="logout-form" action="{{ route('merchant.logout') }}" method="POST"
              style="display: none;">
            {{ csrf_field() }}
        </form>
    </li>
</ul>
