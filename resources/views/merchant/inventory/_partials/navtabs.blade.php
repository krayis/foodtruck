<ul class="nav nav-tabs nav-tabs--darker">
    <li role="presentation"
        class="{{strpos(request()->route()->getAction()['controller'], 'TemplateController') !== false ? 'active' : null }}">
        <a href="{{ route('merchant.inventory.templates.index') }}">Inventory Templates</a></li>
    <li role="presentation"
        class="{{strpos(request()->route()->getAction()['controller'], 'SheetController') ? 'active' : null }}">
        <a href="{{ route('merchant.inventory.sheets.index') }}">Inventory Sheets</a></li>

</ul>

@include('merchant._shared.alerts')
