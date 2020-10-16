<ul class="nav nav-sidebar">
    <li class="nav-brand">
        FoodTruck.am
    </li>
    <li class="{{strpos(URL::current(),'/admin/orders') !== false ? "active" : null }}"><a
            href="{{ route('truck.orders.index') }}">
            <ion-icon name="laptop"></ion-icon>
            Orders</a></li>
    <li class="{{strpos(URL::current(),'/admin/status' ) ? "active" : null}}"><a
            href="{{ route('truck.status.index') }}">
            <ion-icon name="time"></ion-icon>
            My Status</a></li>
    <li class="{{strpos(URL::current(),'/admin/menu' ) ? "active" : null}}"><a
            href="{{ route('truck.menu.item.index') }}">
            <ion-icon name="document"></ion-icon>
            Menu</a></li>
    <li class="{{strpos(URL::current(),'/admin/inventory' ) ? "active" : null}}"><a
            href="{{ route('truck.inventory.index') }}">
            <ion-icon name="cube"></ion-icon>
            Inventory</a></li>
    <li class="{{strpos(URL::current(),'/admin/schedule') ? "active" : null}}"><a
            href="{{ route('truck.schedule.index') }}">
            <ion-icon name="calendar"></ion-icon>
            Schedule</a></li>
    <li class="{{strpos(URL::current(),'/admin/location') ? "active" : null}}"><a
            href="{{ route('truck.location.index') }}">
            <ion-icon name="pin"></ion-icon>
            Locations</a></li>
    <li class="{{strpos(URL::current(),'/admin/settings') ? "active" : null}}"><a
            href="{{ route('truck.settings.index') }}">
            <ion-icon name="cog"></ion-icon>
            Settings</a></li>
    <li>
        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <ion-icon name="log-out"></ion-icon>
            Logout
        </a>
        <form id="logout-form" action="{{ route('truck.logout') }}" method="POST"
              style="display: none;">
            {{ csrf_field() }}
        </form>
    </li>
</ul>
