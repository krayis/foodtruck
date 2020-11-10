@extends('truck.layouts.admin.layout')

@section('content')

    @include('truck.inventory._partials.navtabs')

    <form action="{{ route('admin.inventory.templates.update', $template->id) }}" method="POST"
          enctype="multipart/form-data" autocomplete="off">
        @csrf
        @method('PATCH')
        <div class="meta-header">
            <div class="meta-inner">
                <a href="{{ route('admin.inventory.templates.index') }}" class="back">
                    <ion-icon name="arrow-back"></ion-icon>
                </a>
                <div class="meta-buttons">
                    <button type="button" class="btn btn-grey" data-action="delete" data-target="form-delete">
                        Delete
                    </button>
                    <button class="btn btn-primary">Save</button>
                </div>
            </div>
            <div class="form-group form-group--title @error('name') has-error @enderror">
                <input name="name" type="text" class="form-control form-control" value="{{ $template->name }}"
                       autocomplete="false" placeholder="Name" required/>
                @error('name')
                <div class="help-block" role="alert">
                    <strong>{{ $message }}</strong>
                </div>
                @enderror
            </div>
        </div>
        <table class="table table-align-vertical">
            <thead>
            <tr>
                <th>Item</th>
                <th width="1%" class="white-space--nowrap">Quantity</th>
            </tr>
            </thead>
            <tbody>
            @foreach($items as $item)
                <tr class="@error('items.' .$item->id) has-error @enderror">
                    <td>{{ $item->name }}
                        @error('items.' .$item->id)
                        @foreach($errors->get('items.'.$item->id) as $error)
                            <p class="help-block">Please use a valid quantity.</p>
                        @endforeach
                        @enderror
                    </td>
                    <td><input type="text" name="items[{{$item->id}}]" class="form-control input-sm"
                               value="{{ isset($inventoryItems[$item->id]) ? $inventoryItems[$item->id] : 0 }}"/></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </form>
    <form action="{{ route('admin.inventory.templates.destroy', $template->id) }}" id="form-delete"
          style="display: none;"
          method="POST">
        @csrf
        @method('DELETE')
    </form>
    <script>
        $('[data-action="delete"]').on('click', function (e) {
            e.preventDefault();
            if (confirm('Are you sure you want to delete?')) {
                $('#' + $(this).data('target')).submit();
            }
        });
    </script>
@endsection
