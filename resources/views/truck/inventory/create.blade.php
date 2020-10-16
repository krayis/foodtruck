@extends('truck.layouts.admin.layout')

@section('content')

    @foreach (['danger', 'warning', 'success', 'info'] as $message)
        @if(Session::has($message))
            <p class="alert alert-{{ $message }}">{{ Session::get($message) }}</p>
        @endif
    @endforeach


    <form action="{{ route('truck.inventory.store') }}" method="POST" enctype="multipart/form-data" autocomplete="off">
        @csrf
        <div class="meta-header">
            <div class="meta-inner">
                <a href="{{ route('truck.inventory.index') }}" class="back">
                    <ion-icon name="arrow-back"></ion-icon>
                </a>
                <div class="meta-buttons">
                    <button class="btn btn-primary">Save</button>
                </div>
            </div>
            <div class="form-group form-group--title @error('name') has-error @enderror">
                <input name="name" type="text" class="form-control form-control" value="{{ old('name') }}"
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
                               value="{{ old( 'items[0]', 0) }}"/></td>
                </tr>
            @endforeach
            </tbody>
        </table>


    </form>
@endsection
