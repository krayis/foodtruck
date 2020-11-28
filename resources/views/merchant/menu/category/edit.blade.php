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
            <div class="form-group form-group--lg @error('name') has-error @enderror">
                <input name="name" type="text" class="form-control form-control--title" placeholder="Category name"
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
                    <textarea name="description" class="form-control">{{ $category->description }}</textarea>
                    @error('description')
                    <div class="help-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </div>
                    @enderror
                </div>
            </div>
        </div>
    </form>
    <form action="{{ route('merchant.menu.category.destroy', $category->id) }}" id="delete-form" style="display: none;" method="POST">
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
