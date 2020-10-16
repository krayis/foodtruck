@extends('truck.layouts.admin.layout')

@section('content')

    @include('truck.menu._partials.navtabs')

    <div class="page-header">
        <h1 class="inline-block">Categories</h1>
        <div class="page-action">
            <a class="btn btn-primary" href="{{ route('truck.menu.category.create') }}"><ion-icon name="add"></ion-icon> New Category</a>
        </div>
    </div>


    <table class="table">
        <thead>
        <tr>
            <th>Name</th>
            <th width="1%" class="white-space--nowrap"># of Items</th>
            <th width="1%">Status</th>
        </tr>
        </thead>
        <tbody id="sortable">
        @foreach($categories as $category)
            <tr data-item-id="{{$category->id}}" data-sort-order="{{$category->sort_order}}">
                <td>
                    <a href="{{ route('truck.menu.category.show', $category->id) }}">{{ $category->name }}</a>
                </td>
                <td align="center">
                    <a href="{{ route('truck.menu.category.show', $category->id) }}">
                        {{ $category->items->count() }}
                    </a>
                </td>
                <td>
                    {{ $category->active === 1 ? 'Active' : 'Disable' }}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <script>
        $('.js-table-delete').on('click', function (e) {
            e.preventDefault();
            if (confirm('Are you sure you want to delete?')) {
                $(this).closest('td').find('form.category-delete').submit();
            }
        });
        $('.js-table-toggle-state').on('click', function (e) {
            e.preventDefault();
            if (confirm('Are you sure you want to disable? Items under the category will be hidden from customers.')) {
                $(this).closest('td').find('form.category-toggle-state').submit();
            }
        });
    </script>
@endsection
