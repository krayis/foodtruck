@extends('merchant.layouts.admin.layout')

@section('content')

    @include('merchant.menu._partials.navtabs')
    <form action="{{ route('merchant.menu.category.update', $category->id) }}" method="POST">
        @csrf
        @method('PATCH')
        <div class="meta-header">
            <div class="meta-inner">
                <a href="{{ route('merchant.menu.category.index') }}" class="back">
                    <ion-icon name="arrow-back"></ion-icon>
                </a>
                <div class="meta-buttons">
                    <button type="button" class="btn btn-grey" data-action="delete" data-target="delete-form">Delete
                    </button>
                    <button class="btn btn-primary">Save</button>
                </div>
            </div>
            <div class="form-group form-group--title @error('name') has-error @enderror">
                <input name="name" type="text" class="form-control" placeholder="Category name"
                       value="{{ $category->name }}"
                       required>
                @error('name')
                <div class="help-block" role="alert">
                    <strong>{{ $message }}</strong>
                </div>
                @enderror
            </div>
        </div>

        <div class="row">
            <div class="col-md-16">
                <div class="form-group @error('description') has-error @enderror">
                    <label for="">Description</label>
                    <textarea name="description" class="form-control" rows="3">{{ $category->description }}</textarea>
                    @error('description')
                    <div class="help-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </div>
                    @enderror
                </div>
            </div>
        </div>
        <hr/>
        <div class="row">
            <div class="col-md-16">
                <h3>Items</h3>
                @if($category->items()->exists())
                    <ul class="group-modifier-list" id="sortable">
                        @foreach($category->items as $key => $item)
                            <li data-key="{{$key}}">
                                <div class="inner">
                                    <div class="reorder">
                                        <ion-icon name="reorder"></ion-icon>
                                    </div>
                                    <div>
                                        {{ $item->name }}
                                    </div>
                                    <span class="more">
                                    <div class="dropdown">
                                        <a href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                            <ion-icon name="more"></ion-icon>
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenu1">
                                            <li>
                                                <a href="{{ route('merchant.menu.item.edit', $item->id ) }}">
                                                    Remove
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ route('merchant.menu.item.edit', $item->id ) }}">Edit Item
                                                </a>
                                            </li>
                                        </ul>
                             </div>
                        </span>
                                </div>

                            </li>
                        @endforeach
                    </ul>
                @else
                    No items added
                @endif
            </div>
        </div>
    </form>
    <form action="{{ route('merchant.menu.category.destroy', $category->id) }}" id="delete-form" style="display: none;"
          method="POST">
        @csrf
        @method('DELETE')
    </form>

    <script>
        var $list = $("#sortable");
        $list.sortable({
            handle: '.reorder',
            stop: function (evt, ui) {
                $list.find('> li').each(function (i, item) {
                    var $item = $(item);
                    $item.find('[name="items[' + $item.data('key') + '][sort_order]"]').val(i);
                });

            }
        });
        $('[data-action="delete"]').on('click', function (e) {
            e.preventDefault();
            if (confirm('Are you sure you want to delete?')) {
                $('#' + $(this).data('target')).submit();
            }
        });
    </script>
@endsection
