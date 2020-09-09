@include('truck.layouts.admin.head')
@include('truck.layouts.admin.nav')
<div class="container">
    <div class="flash-message">
        @foreach (['danger', 'warning', 'success', 'info'] as $message)
            @if(Session::has($message))
                <p class="alert alert-{{ $message }}">{{ Session::get($message) }}</p>
            @endif
        @endforeach
    </div>
</div>
@include('truck.menu._partials.navtabs')
<div class="container">
    <div class="page-header">
        <h1>Create Category</h1>
    </div>
    <div class="row">
        <div class="col-sm-24 col-md-12">

                <form action="{{ route('truck.menu.category.store') }}" method="POST">
                    @csrf
                    <div class="form-group @error('name') has-error @enderror">
                        <label for="">Category name</label>
                        <input name="name" type="text" class="form-control" value="{{ old('name') }}" required>
                        @error('name')
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
                    <button class="btn btn-primary">Save</button>
                </form>

        </div>
    </div>
</div>
@include('truck.layouts.client.footer')
