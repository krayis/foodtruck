@include('truck.layouts.admin.head')
@include('truck.layouts.admin.nav')
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
                url: '{{ route('truck.menu.category.item.sort', $category->id) }}',
                data: data
            });

            request.done(function (data) {

            });
        }
    });
</script>
@include('truck.layouts.client.footer')
