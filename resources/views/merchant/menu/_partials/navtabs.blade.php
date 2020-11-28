<ul class="nav nav-tabs nav-tabs--darker">
    <li role="presentation"
        class="{{strpos(request()->route()->getAction()['controller'], 'MenuController') !== false ? 'active' : null }}">
        <a href="{{ route('merchant.menu.index') }}">Overview</a></li>
    <li role="presentation"
        class="{{strpos(request()->route()->getAction()['as'], 'merchant.menu.item') !== false && strpos(request()->route()->getAction()['as'], 'merchant.menu.item.modifier') === false ? 'active' : null }}">
        <a href="{{ route('merchant.menu.item.index') }}">Items</a></li>
    <li role="presentation"
        class="{{strpos(request()->route()->getAction()['as'], 'merchant.menu.category') !== false ? 'active' : null }}"><a
            href="{{ route('merchant.menu.category.index') }}">Categories</a></li>
    <li role="presentation"
        class="{{strpos(request()->route()->getAction()['controller'], 'ModifierController') !== false ? 'active' : null }}"><a
            href="{{ route('merchant.menu.modifier.index') }}">Modifiers
        </a></li>
    <li role="presentation"
        class="{{strpos(request()->route()->getAction()['controller'], 'GroupController') !== false ? 'active' : null }}"><a
            href="{{ route('merchant.menu.modifier.group.index')  }}">Modifier Groups
        </a></li>
</ul>

@include('merchant._shared.alerts')
