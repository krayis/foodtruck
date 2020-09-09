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
        <h1>Update Item</h1>
    </div>

    <div class="row">
        <div class="col-sm-12">

            <form action="{{ route('truck.menu.item.update', $item->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PATCH')
                <div class="form-group @error('name') has-error @enderror">
                    <label for="">Item name</label>
                    <input name="name" type="text" class="form-control" value="{{ $item->name }}" required>
                    @error('name')
                    <div class="help-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </div>
                    @enderror
                </div>
                <div class="form-group @error('category_id') has-error @enderror">
                    <label for="">Category</label>
                    <select name="category_id" class="form-control">
                        <option value=""></option>
                        @foreach($categories as $category)
                            <option
                                value="{{ $category->id }}" {{ $item->category_id === $category->id ? 'selected' : null }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id')
                    <div class="help-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </div>
                    @enderror
                </div>
                <div class="form-group @error('price') has-error @enderror">
                    <label for="">Price</label>
                    <input name="price" type="text" class="form-control" value="{{ $item->price }}" required>
                    @error('price')
                    <div class="help-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </div>
                    @enderror
                </div>
                <div class="form-group @error('description') has-error @enderror">
                    <label for="">Description</label>
                    <textarea name="description" class="form-control">{{ $item->description }}</textarea>
                    @error('description')
                    <div class="help-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </div>
                    @enderror
                </div>
                <div class="form-group @error('thumbnail') has-error @enderror">
                    <label for="">Thumbnail</label>
                    <input type="file" name="thumbnail">
                    @if (isset($item->thumbnail))
                        <br/>
                        <img src="{{ asset('storage/' . $item->thumbnail)  }}" class="img-thumbnail"/>
                    @endif
                    @error('thumbnail')
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
