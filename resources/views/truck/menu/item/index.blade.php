@include('truck.layouts.admin.head')
@include('truck.layouts.admin.nav')
<div class="container">
    <div class="flash-message">
        @foreach (['danger', 'warning', 'success', 'info'] as $message)
            @if(Session::has($message))
                <p class="alert alert-{{ $message }}">{{ Session::get($message) }}</p>
            @endif
        @endforeach
    </div>
</div>
@include('truck.menu._partials.navtabs')
<div class="container">
    <div class="page-header">
        <h1 class="inline-block">Items</h1>
        <div class="page-action">
            <a class="btn btn-primary" href="{{ route('truck.menu.item.create') }}">Add Item</a>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-sm-24">
                <table class="table">
                    <thead>
                    <tr>
                        <th></th>
                        <th>Name</th>
                        <th>Modifiers</th>
                        <th>Description</th>
                        <th width="25%">Category</th>
                        <th width="1%">Price</th>
                        <th width="1%"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($items as $item)
                        <tr>
                            <td width="1%" class="pd-0 {{ $item->active === 1 ? 'enabled' : 'disabled' }}">
                                <span class="table-icon table-icon-padding">
                                    <ion-icon
                                        name="{{ $item->active === 1 ? 'ios-radio-button-on' : 'ios-pause' }}"></ion-icon>
                                </span>
                            </td>
                            <td width="1%" class="white-space--nowrap">{{ $item->name }}</td>
                            <td>
                                @foreach($item->modifierCategories as $category)
                                    <div><a href="{{ route('truck.menu.modifier.category.show', $category->id/**/) }}">{{ $category->name }}</a></div>
                                @endforeach
                            </td>
                            <td>{{ $item->description }}</td>
                            <td>{{ $item->category ? $item->category->name : null }}</td>
                            <td>${{ $item->price }}</td>
                            <td>
                                <form action="{{ route('truck.menu.item.destroy',  $item->id) }}" class="item-delete"
                                      style="display: none;"
                                      method="POST">
                                    @csrf
                                    @method('DELETE')
                                </form>
                                <form action="{{ route('truck.menu.item.update',  $item->id) }}"
                                      class="item-toggle-state" style="display: none;" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="active" value="{{ $item->active === 1 ? 0 : 1 }}"/>
                                </form>
                                <div class="dropdown">
                                    <a href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                        <ion-icon name="ios-more"></ion-icon>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenu1">
                                        <li><a href="{{ route('truck.menu.item.edit', $item->id ) }}">
                                                <ion-icon name="ios-create"></ion-icon>
                                                Edit</a></li>
                                        <li><a href="#" class="js-table-toggle-state">
                                                <ion-icon
                                                    name="{{ $item->active === 1 ? 'pause' : 'ios-radio-button-on' }}"></ion-icon> {{ $item->active === 1 ? 'Disable' : 'Enabled' }}
                                            </a></li>
                                        <li><a href="#" class="js-table-delete">
                                                <ion-icon name="ios-trash"></ion-icon>
                                                Delete</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    $('.js-table-delete').on('click', function (e) {
        e.preventDefault();
        if (confirm('Are you sure you want to delete?')) {
            $(this).closest('td').find('form.item-delete').submit();
        }
    });
    $('.js-table-toggle-state').on('click', function (e) {
        e.preventDefault();
        if (confirm('Are you sure you want to disable?')) {
            $(this).closest('td').find('form.item-toggle-state').submit();
        }
    });
</script>
@include('truck.layouts.client.footer')
