<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="{{ route('truck.orders.index') }}">FoodTruck.me</a>
        </div>
        <div id="navbar" class="collapse navbar-right navbar-collapse">
            <ul class="nav navbar-nav">
                <li class="{{strpos(URL::current(),'/truck/orders') !== false ? "active" : null }}"><a href="{{ route('truck.orders.index') }}">Orders</a></li>
                <li class="{{strpos(URL::current(),'/truck/status' ) ? "active" : null}}"><a href="{{ route('truck.status.index') }}">My Status</a></li>
                <li class="{{strpos(URL::current(),'/truck/menu' ) ? "active" : null}}"><a href="{{ route('truck.menu.item.index') }}">Menu</a></li>
                <li class="{{strpos(URL::current(),'/truck/schedule') ? "active" : null}}"><a href="{{ route('truck.schedule.index') }}">Schedule</a></li>
                <li class="{{strpos(URL::current(),'/truck/location') ? "active" : null}}"><a href="{{ route('truck.location.index') }}">Locations</a></li>
                <li class="dropdown {{strpos(URL::current(),'/truck/settings') ? "active" : null}}">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"> <ion-icon name="bus"></ion-icon> <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li><a href="{{ route('truck.settings.index') }}">Settings</a></li>
                        <li>
                            <a href="#"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                Logout
                            </a>

                            <form id="logout-form" action="{{ route('truck.logout') }}" method="POST" style="display: none;">
                                {{ csrf_field() }}
                            </form>
                        </li>

                    </ul>
                </li>
            </ul>
        </div><!--/.nav-collapse -->
    </div>
</nav>


