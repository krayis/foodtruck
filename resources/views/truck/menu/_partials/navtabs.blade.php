<div class="container">
    <ul class="nav nav-tabs">
        <li role="presentation" class="{{strpos(request()->route()->getAction()['as'], 'truck.menu.item') !== false && strpos(request()->route()->getAction()['as'], 'truck.menu.item.modifier') === false ? 'active' : null }}"><a href="{{ route('truck.menu.item.index') }}">Items</a></li>
        <li role="presentation" class="{{strpos(request()->route()->getAction()['as'], 'truck.menu.category') !== false ? 'active' : null }}"><a href="{{ route('truck.menu.category.index') }}">Categories</a></li>

        <li role="presentation" class="dropdown {{strpos(request()->route()->getAction()['as'], 'truck.menu.modifier') !== false ? 'active' : null }}">
            <a href="#" data-toggle="dropdown" aria-haspopup="true">
                Modifiers <ion-icon name="ios-arrow-down" class="ios-arrow-down"></ion-icon>
            </a>
            <ul class="dropdown-menu"
                aria-labelledby="dropdownMenu1">
                <li><a href="{{ route('truck.menu.modifier.overview') }}">Overview</a></li>
                <li><a href="{{ route('truck.menu.modifier.index') }}">Modifier</a></li>
                <li><a href="{{ route('truck.menu.modifier.category.index') }}">Categories</a></li>
            </ul>
        </li>
    </ul>
</div>

