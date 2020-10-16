@extends('truck.layouts.admin.layout')

@section('content')

    @include('truck.menu._partials.navtabs')

    <div class="page-header">
        <h1 class="inline-block">Items</h1>
        <div class="page-action">
            <a class="btn btn-primary" href="{{ route('truck.menu.item.create') }}"><ion-icon name="add"></ion-icon> New Item</a>
        </div>
    </div>


    <div class="row">
        <div class="col-sm-24">
            <table class="table">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Modifiers</th>
                    <th>Description</th>
                    <th width="25%">Category</th>
                    <th width="1%">Price</th>
                    <th width="1%">Status</th>
                </tr>
                </thead>
                <tbody>
                @foreach($items as $item)
                    <tr>
                        <td width="1%" class="white-space--nowrap">
                            <a href="{{ route('truck.menu.item.edit', $item->id ) }}">
                                {{ $item->name }}
                            </a>
                        </td>
                        <td>
                            @foreach($item->modifierGroups as $category)
                                <div>
                                    <a href="{{ route('truck.menu.modifier.group.show', $category->id/**/) }}">{{ $category->name }}</a>
                                </div>
                            @endforeach
                        </td>
                        <td>{{ $item->description }}</td>
                        <td>{{ $item->category ? $item->category->name : null }}</td>
                        <td>${{ $item->price }}</td>
                        <td>{{ $item->active === 1 ? 'Active' : 'Disabled' }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection



