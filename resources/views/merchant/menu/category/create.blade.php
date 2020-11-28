@extends('merchant.layouts.admin.layout')

@section('content')
    @include('merchant.menu._partials.navtabs')
    <form action="{{ route('merchant.menu.category.store') }}" method="POST">
        @csrf
        <div class="meta-header">
            <div class="meta-inner">
                <a href="{{ url()->previous() }}" class="back">
                    <ion-icon name="arrow-back"></ion-icon>
                </a>
                <div class="meta-buttons">
                    <button class="btn btn-primary">Save</button>
                </div>
            </div>
            <div class="form-group form-group--title @error('name') has-error @enderror">
                <input name="name" type="text" class="form-control form-control" placeholder="Category name" value="{{ old('name') }}" required>
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
                    <textarea name="description" class="form-control" value="{{ old('description') }}" rows="3"></textarea>
                    @error('description')
                    <div class="help-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </div>
                    @enderror
                </div>
            </div>
        </div>
    </form>
@endsection
