@extends('merchant.layouts.admin.layout')

@section('content')

    @foreach (['danger', 'warning', 'success', 'info'] as $message)
        @if(Session::has($message))
            <p class="alert alert-{{ $message }}">{{ Session::get($message) }}</p>
        @endif
    @endforeach

    @include('merchant.menu._partials.navtabs')

    <form action="{{ route('merchant.menu.item.store') }}" method="POST" enctype="multipart/form-data" autocomplete="off">
        @csrf
        <div class="meta-header">
            <div class="meta-inner">
                <a href="{{ route('merchant.menu.item.index') }}" class="back">
                    <ion-icon name="arrow-back"></ion-icon>
                </a>
                <div class="meta-buttons">
                    <button class="btn btn-primary">Save</button>
                </div>
            </div>
            <div class="form-group form-group--title @error('name') has-error @enderror">
                <input name="name" type="text" class="form-control form-control" value="{{ old('name') }}"
                       autocomplete="false" placeholder="Item name" required>
                @error('name')
                <div class="help-block" role="alert">
                    <strong>{{ $message }}</strong>
                </div>
                @enderror
            </div>
        </div>


        <div class="row">
            <div class="col-md-16">
                <div class="form-group @error('category_id') has-error @enderror">
                    <label for="">Category</label>
                    <select name="category_id" class="form-control">
                        @if(count($categories) > 0)
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        @else
                            <option disabled="disabled">Please create a category first.</option>
                        @endif
                    </select>
                    @error('category_id')
                    <div class="help-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </div>
                    @enderror
                </div>
                <div class="form-group @error('price') has-error @enderror">
                    <label for="">Price</label>
                    <input name="price" type="text" class="form-control" value="{{ old('price') }}" required>
                    @error('price')
                    <div class="help-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </div>
                    @enderror
                </div>
                <div class="form-group @error('description') has-error @enderror">
                    <label for="">Description</label>
                    <textarea name="description" class="form-control" value="{{ old('description') }}"></textarea>
                    @error('description')
                    <div class="help-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </div>
                    @enderror
                </div>
                <div class="form-group @error('thumbnail') has-error @enderror">
                    <label for="">Thumbnail</label>
                    <input type="file" name="thumbnail">
                    @error('thumbnail')
                    <div class="help-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </div>
                    @enderror
                </div>
            </div>
        </div>
    </form>
@endsection
