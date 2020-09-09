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
        <h1>{{ $category->name }}</h1>
        @if($category->description)
            <p>{{ $category->description }}</p>
        @endif
    </div>
    <div class="content">
        <table class="table">
            <thead>
            <tr>
                <th width="1%"></th>
                <th width="1%"></th>
                <th>Item Name</th>
                <th>Description</th>
                <th width="1%">Price</th>
                <th width="1%"></th>
            </tr>
            </thead>
            <tbody id="sortable">
            @foreach($category->modifiers as $modifier)
                <tr data-item-id="{{$modifier->id}}" data-sort-order="{{$modifier->sort_order}}">
                    <td class="pd-0" width="1%">
                        <span class="handle table-icon table-icon-padding cursor-grab">
                            <ion-icon name="ios-reorder"></ion-icon>
                        </span>
                    </td>
                    <td width="1%" class="pd-0 {{ $modifier->active === 1 ? 'enabled' : 'disabled' }}">
                        <span class="table-icon table-icon-padding">
                            <ion-icon
                                name="{{ $modifier->active === 1 ? 'ios-radio-button-on' : 'ios-pause' }}"></ion-icon>
                        </span>
                    </td>
                    <td class="white-space--nowrap">{{ $modifier->name }}</td>
                    <td>{{ $modifier->description }}</td>
                    <td>${{ $modifier->price }}</td>
                    <td align="right">
                        <form action="{{ route('truck.menu.modifier.destroy', $modifier->id) }}"
                              class="delete" style="display: none;" method="POST">
                            @csrf
                            @method('DELETE')
                        </form>
                        <form action="{{ route('truck.menu.modifier.update',  $modifier->id) }}" class="toggle-state"
                              style="display: none;" method="POST">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="active" value="{{ $modifier->active === 1 ? 0 : 1 }}"/>
                        </form>
                        <div class="dropdown">
                            <a href="#" data-toggle="dropdown" aria-haspopup="true"
                               aria-expanded="true">
                                <ion-icon name="ios-more" role="img"
                                          class="md hydrated"
                                          aria-label="ellipsis vertical outline"></ion-icon>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-right"
                                aria-labelledby="dropdownMenu1">
                                <li><a href="#" class="js-table-toggle-state">
                                        <ion-icon
                                            name="{{ $modifier->active === 1 ? 'pause' : 'ios-radio-button-on' }}"></ion-icon> {{ $modifier->active === 1 ? 'Disable' : 'Enabled' }}
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
<script>
    function fixWidthHelper(e, ui) {
        ui.children().each(function() {
            $(this).width($(this).width());
        });
        return ui;
    }
    var $table = $( "#sortable" );
    $table.sortable({
        helper: fixWidthHelper,
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
                url: '{{ route('truck.menu.modifier.category.modifier.sort', $category->id) }}',
                data: data
            });

            request.done(function (data) {

            });
        }
    });
</script>
<script>
    $('.js-table-delete').on('click', function (e) {
        e.preventDefault();
        if (confirm('Are you sure you want to delete?')) {
            $(this).closest('td').find('form.delete').submit();
        }
    });
    $('.js-table-toggle-state').on('click', function (e) {
        e.preventDefault();
        if (confirm('Are you sure you want to disable?')) {
            $(this).closest('td').find('form.toggle-state').submit();
        }
    });
</script>
@include('truck.layouts.client.footer')
