@extends('truck.layouts.admin.layout')

@section('content')

    @include('truck.inventory._partials.navtabs')

    <div class="page-header">
        <h1 class="inline-block">Inventory Templates</h1>
        <div class="page-action">
            <a class="btn btn-primary" href="{{ route('admin.inventory.templates.create') }}">
                <ion-icon name="add"></ion-icon>
                New Template</a>
        </div>
    </div>

    <table class="table">
        <thead>
        <tr>
            <th>Name</th>
            <th width="1%" class="white-space--nowrap">Assigned Dates</th>
        </tr>
        </thead>
        <tbody>
        @if (count($inventories))
            @foreach ($inventories as $sheet)
                <tr>
                    <td>
                        <a href="{{ route('admin.inventory.templates.edit', $sheet->id) }}">
                            {{ $sheet->name }}
                        </a>
                    </td>
                    <td></td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="2">
                    No inventories sheets added.
                </td>
            </tr>
        @endif
        </tbody>
    </table>

@endsection
@include('truck.layouts.client.footer')
