@extends('merchant.layouts.admin.layout')

@section('content')

    @include('merchant.inventory._partials.navtabs')

    <div class="page-header">
        <h1 class="inline-block">Inventory Sheets</h1>
        <div class="page-action">
            <a class="btn btn-primary" href="{{ route('merchant.inventory.sheets.create') }}">
                <ion-icon name="add"></ion-icon>
                New Sheet
            </a>
        </div>
    </div>

    <table class="table">
        <thead>
        <tr>
            <th>Name</th>
            <th width="1%" class="white-space--nowrap">Assigned</th>
        </tr>
        </thead>
        <tbody>
        @if (count($sheets))
            @foreach ($sheets as $sheet)
                <tr>
                    <td>
                        <a href="{{ route('merchant.inventory.sheets.edit', $sheet->id) }}">
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
@include('merchant.layouts.client.footer')
