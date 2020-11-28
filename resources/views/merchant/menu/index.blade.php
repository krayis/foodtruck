@extends('merchant.layouts.admin.layout')

@section('content')

    @include('merchant.menu._partials.navtabs')

    <form action="{{ route('merchant.menu.store') }}" method="POST" enctype="multipart/form-data" autocomplete="off">
        @csrf
        <div class="row">
            <div class="col-md-16">
                <div class="page-header">
                    <h1 class="inline-block">Overview</h1>
                    @if ($categories->count())
                        <div class="page-action">
                            <button class="btn btn-primary">Save</button>
                        </div>
                    @endif
                </div>

                @if ($categories->count())
                    <ul class="menu sortable">
                        @foreach($categories as $key => $category)
                            <li class="section" data-key="{{$key}}" data-name="categories">
                                <div class="item">
                                    <div class="item-wrapper">
                                        <div class="reorder">
                                            <ion-icon name="reorder"></ion-icon>
                                        </div>
                                        <div>
                                            {{ $category->name }}
                                            <input type="hidden" name="categories[{{$key}}][id]"
                                                   value="{{ $category->id }}">
                                            <input type="hidden" data-key="{{$key}}"
                                                   name="categories[{{$key}}][sort_order]"
                                                   value="{{ $category->sort_order }}">
                                        </div>
                                        <div class="more">
                                            <div class="dropdown">
                                                <a href="#" data-toggle="dropdown" aria-haspopup="true"
                                                   aria-expanded="true">
                                                    <ion-icon name="more"></ion-icon>
                                                </a>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a href="{{ route('merchant.menu.category.show', $category->id ) }}">
                                                            Edit Category
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @if (count($category->items))
                                    <ul class="sortable">
                                        @foreach($category->items as $key => $item)
                                            <li class="item" data-key="{{$key}}" data-name="items">
                                                <div class="item-wrapper">
                                                    <div class="reorder">
                                                        <ion-icon name="reorder"></ion-icon>
                                                    </div>
                                                    <div>
                                                        {{ $item->name }}
                                                        <input type="hidden" name="items[{{$key}}][id]"
                                                               value="{{ $item->id }}">
                                                        <input type="hidden" data-key="{{$key}}"
                                                               name="items[{{$key}}][sort_order]"
                                                               value="{{ $item->sort_order }}">
                                                    </div>
                                                    <div class="more">
                                                        <div class="dropdown">
                                                            <a href="#" data-toggle="dropdown" aria-haspopup="true"
                                                               aria-expanded="true">
                                                                <ion-icon name="more"></ion-icon>
                                                            </a>
                                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                                                                <li>
                                                                    <a href="{{ route('merchant.menu.item.edit', $item->id ) }}">
                                                                        Out of stock
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a href="{{ route('merchant.menu.modifier.group.edit', $item->id ) }}">
                                                                        Remove from category
                                                                    </a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @else
                    @if ($itemCount)
                        <p class="alert alert-warning">
                            Please assign categories to your items.<br/>
                            <strong><a href="{{ route('merchant.menu.category.index') }}">Create a
                                    category</a></strong><br/>
                            <strong><a href="{{ route('merchant.menu.item.index') }}">Assign category to
                                    item</a></strong>
                        </p>
                    @else
                        <p class="alert alert-warning">
                            You have not created a category or items.<br/>
                            <strong><a href="{{ route('merchant.menu.category.index') }}">Create a
                                    category</a></strong><br/>
                            <strong><a href="{{ route('merchant.menu.item.index') }}">Assign item to
                                    category</a></strong>
                        </p>
                    @endif

                @endif
            </div>
        </div>
    </form>

    <script>
        var modal = $('#itemModal');
        $('.js-edit-item').on('click', function (e) {
            e.preventDefault();
            $('#itemModal').modal('show');
            var item = $(this).closest('.card--item');
            modal.find("form").attr('action', '/truck/menu/item/' + item.find("[name='category_id']").val());
            modal.find("[name='name']").val(item.find("[name='name']").val());
            modal.find("[name='price']").val(item.find("[name='price']").val());
            modal.find("[name='category_id']").val(item.find("[name='category_id']").val());
            modal.find("[name='description']").val(item.find("[name='description']").val());
        });
    </script>

    <script>
        var $list = $(".sortable");
        $list.sortable({
            handle: '.reorder',
            stop: function (evt, ui) {
                $list.find('> li').each(function (i, item) {
                    var $item = $(item);
                    $item.find('[name="' + $item.data('name') + '[' + $item.data('key') + '][sort_order]"]').val(i);
                });

            }
        });
    </script>
@endsection
@include('merchant.layouts.client.footer')
