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
        <h1>Update Category</h1>
    </div>
    <div class="row">
        <div class="col-sm-24 col-md-12">

                <form action="{{ route('truck.menu.category.update', $category->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="form-group @error('name') has-error @enderror">
                        <label for="">Category Name</label>
                        <input name="name" type="text" class="form-control" value="{{ $category->name }}" required>
                        @error('name')
                        <div class="help-block" role="alert">
                            <strong>{{ $message }}</strong>
                        </div>
                        @enderror
                    </div>
                    <div class="form-group @error('description') has-error @enderror">
                        <label for="">Description</label>
                        <textarea name="description" class="form-control">{{ $category->description }}</textarea>
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
