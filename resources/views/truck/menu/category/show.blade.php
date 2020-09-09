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
                <th>Item Name</th>
                <th>Description</th>
                <th width="1%">Price</th>
            </tr>
            </thead>
            <tbody id="sortable">
            @foreach($category->items as $item)
                <tr data-item-id="{{$item->id}}" data-sort-order="{{$item->sort_order}}">
                    <td class="pd-0" width="1%">
                        <span class="handle table-icon table-icon-padding cursor-grab">
                            <ion-icon name="ios-reorder"></ion-icon>
                        </span>
                    </td>
                    <td width="1%" class="white-space--nowrap">{{ $item->name }}</td>
                    <td>{{ $item->description }}</td>
                    <td>${{ $item->price }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
<script>
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
                url: '{{ route('truck.menu.category.item@sortItem', $category->id) }}',
                data: data
            });
        }
    });
</script>
@include('truck.layouts.client.footer')
