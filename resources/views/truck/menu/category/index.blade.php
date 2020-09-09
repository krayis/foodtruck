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
        <h1 class="inline-block">Categories</h1>
        <div class="page-action">
            <a class="btn btn-primary" href="{{ route('truck.menu.category.create') }}">Add Category</a>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-sm-24">
            </div>
            <div class="col-sm-24">
                <table class="table">
                    <thead>
                    <tr>
                        <th width="1%"></th>
                        <th width="1%"></th>
                        <th>Name</th>
                        <th width="1%" class="white-space--nowrap"># of Items</th>
                        <th width="1%"></th>
                    </tr>
                    </thead>
                    <tbody id="sortable">
                    @foreach($categories as $category)
                        <tr data-item-id="{{$category->id}}" data-sort-order="{{$category->sort_order}}">
                            <td class="pd-0" width="1%">
                                <span class="handle table-icon table-icon-padding cursor-grab">
                                    <ion-icon name="ios-reorder"></ion-icon>
                                </span>
                            </td>
                            <td width="1%" class="pd-0 {{ $category->active === 1 ? 'enabled' : 'disabled' }}">
                                <span class="table-icon table-icon-padding">
                                    <ion-icon name="{{ $category->active === 1 ? 'ios-radio-button-on' : 'ios-pause' }}"></ion-icon>
                                </span>
                            </td>
                            <td>
                                {{ $category->name }}
                            </td>
                            <td align="center">
                                <a href="{{ route('truck.menu.category.show', $category->id) }}">
                                    {{ $category->items->count() }}
                                </a>
                            </td>
                            <td>
                                <form action="{{ route('truck.menu.category.destroy', $category->id) }}" class="category-delete" style="display: none;" method="POST">
                                    @csrf
                                    @method('DELETE')
                                </form>
                                <form action="{{ route('truck.menu.category.update', $category->id) }}" class="category-toggle-state" style="display: none;" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="active" value="{{ $category->active === 1 ? 0 : 1 }}" />
                                </form>
                                <div class="dropdown">
                                    <a href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                        <ion-icon name="ios-more"></ion-icon>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenu1">
                                        <li><a href="{{ route('truck.menu.category.show', $category->id) }}"><ion-icon name="ios-reorder"></ion-icon> Sort Items</a></li>
                                        <li><a href="{{ route('truck.menu.category.edit', $category->id) }}"><ion-icon name="ios-create"></ion-icon> Edit</a></li>
                                        <li><a href="#" class="js-table-toggle-state"><ion-icon name="{{ $category->active === 1 ? 'pause' : 'ios-radio-button-on' }}"></ion-icon> {{ $category->active === 1 ? 'Disable' : 'Enabled' }}</a></li>
                                        <li><a href="#" class="js-table-delete"><ion-icon name="ios-trash"></ion-icon> Delete</a></li>
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
    $('.js-table-delete').on('click', function(e) {
        e.preventDefault();
        if (confirm('Are you sure you want to delete?')) {
            $(this).closest('td').find('form.category-delete').submit();
        }
    });
    $('.js-table-toggle-state').on('click', function(e) {
        e.preventDefault();
        if (confirm('Are you sure you want to disable? Items under the category will be hidden from customers.')) {
            $(this).closest('td').find('form.category-toggle-state').submit();
        }
    });
    var $table = $( "#sortable" );
    $table.sortable({
        helper: function(e, ui) {
            ui.children().each(function() {
                $(this).width($(this).width());
            });
            return ui;
        } ,
        handle: '.handle',
        stop: function(evt, ui) {
            var sortOrder = ui.item.prev().length ? parseInt(ui.item.prev().data('sort-order')) - 1 : parseInt(ui.item.next().data('sort-order')) + 1;
            var data = {
                id: ui.item.data('item-id'),
                sort_order: sortOrder,
                _token: '{{ csrf_token() }}',
            };
            var request = $.ajax({
                method: 'POST',
                url: '{{ route('truck.menu.category@sortCategory') }}',
                data: data
            });
        }
    });
</script>
@include('truck.layouts.client.footer')
