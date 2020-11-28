@extends('truck.layouts.admin.layout')

@section('content')

@include('truck.menu._partials.navtabs')

    <div class="page-header">
        <h1 class="inline-block">Modifiers</h1>
        <div class="page-action">
            <a class="btn btn-primary" href="{{ route('truck.menu.modifier.create') }}"><ion-icon name="add"></ion-icon> New Modifier</a>
        </div>
    </div>

        <table class="table table--modifier">
            <thead>
            <tr>
                <th>Name</th>
                <th>Modifier Group</th>
                <th>Min/Max</th>
                <th width="1%">Price</th>
                <th width="1%">Status</th>
            </tr>
            </thead>
            <tbody>

            @foreach($modifiers as $modifier)
                <tr>
                    <td><a href="{{ route('truck.menu.modifier.edit', $modifier->id) }}">{{$modifier->name}}</a></td>
                    <td>{{ $modifier->group ? $modifier->group->name : null }}</td>
                    <td class="white-space--nowrap">
                        @if($modifier->type === 'MULTIPLE')
                            Multiple: {{$modifier->min}}-{{$modifier->max}}
                        @else
                            Single Selection
                        @endif
                    </td>
                    <td align="center">${{$modifier->price}}</td>
                    <td>
                        {{ $modifier->active === 1 ? 'Active' : 'Disabled' }}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>


{{ $modifiers->links() }}

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
@endsection
