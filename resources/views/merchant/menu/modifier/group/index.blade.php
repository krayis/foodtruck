@extends('merchant.layouts.admin.layout')

@section('content')
    @include('merchant.menu._partials.navtabs')

    <div class="page-header">
        <h1 class="inline-block">Modifier Groups</h1>
        <div class="page-action">
            <a class="btn btn-primary" href="{{ route('merchant.menu.modifier.group.create') }}"><ion-icon name="add"></ion-icon> New Group</a>
        </div>
    </div>

    <table class="table table--modifier">
        <thead>
        <tr>
            <th>Name</th>
            <th>Contains</th>
            <th width="1%">Status</th>
        </tr>
        </thead>
        <tbody>
        @foreach($categories as $category)
            <tr>
                <td>
                    <a href="{{ route('merchant.menu.modifier.group.edit', $category->id) }}">
                        {{ $category->name }}
                    </a>
                </td>

                <td>
                    @if (count($category->modifiers))
                        {{ implode(', ', $category->modifiers->pluck('name')->toArray()) }}
                    @endif
                </td>
                <td>
                    {{ $category->active === 1 ? 'Active' : 'Disabled' }}

                    <form action="{{ route('merchant.menu.modifier.group.destroy', $category->id) }}"
                          class="delete" style="display: none;" method="POST">
                        @csrf
                        @method('DELETE')
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    {{ $categories->links() }}
    <script>
        $('.js-table-delete').on('click', function (e) {
            e.preventDefault();
            if (confirm('Are you sure you want to delete?')) {
                $(this).closest('td').find('form.delete').submit();
            }
        });
        $('.js-table-toggle-state').on('click', function (e) {
            e.preventDefault();
            if (confirm('Are you sure you want to disable? Items under the category will be hidden from customers.')) {
                $(this).closest('td').find('form.toggle-state').submit();
            }
        });
    </script>
@endsection
