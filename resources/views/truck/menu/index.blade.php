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
    <ul class="menu-list">
        @foreach($categories as $category)
            <li class="category">
                <h2>{{ $category->name }}</h2>
                @if($category->description)
                    <p>{{ $category->description }}</p>
                @endif
                <div class="flex-row">
                    @if(count($category->items))
                        @foreach($category->items as $item)

                                <div class="card--item">
                                <div class="card--item-content">
                                    <div class="card--item-header">
                                            {{ $item->name }}
                                        <div class="card--item-price pull-right">
                                            ${{ $item->price }}
                                        </div>
                                    </div>
                                    <div class="card--item-description {{isset($item->thumbnail) ? 'has-thumbnail' : null }}">
                                        {{ $item->description }}
                                    </div>
                                    <div class="card--item-meta">
                                        <a href="#" class="js-edit-item">Edit</a>
                                    </div>
                                    <input type="hidden" name="name" value="{{ $item->name }}">
                                    <input type="hidden" name="price" value="{{ $item->price }}">
                                    <input type="hidden" name="description" value="{{ $item->description }}">
                                    <input type="hidden" name="category_id" value="{{ $item->category_id }}">
                                    @if (isset($item->thumbnail))
                                        <div class="card--item-preview" style="background-image: url({{asset('storage/' . $item->thumbnail)}})">
                                        </div>
                                    @endif
                                </div>
                                </div>

                        @endforeach
                    @else
                        <div class="col-xs-24">
                            <div class="alert alert-warning">No items added.</div>
                        </div>
                    @endif
                </div>
            </li>
        @endforeach
    </ul>
</div>
<div class="modal fade" id="itemModal" tabindex="-1" role="dialog" aria-labelledby="itemModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route("admin.item.storesssss") }}" method="POST">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="myModalLabel">Edit Item</h4>
                </div>
                <div class="modal-body">
                    @csrf
                    @method('PATCH')
                    <div class="form-group">
                        <label for="">Item Name</label>
                        <input name="name" type="text" class="form-control" value="" required>
                    </div>
                    <div class="form-group">
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
                    <div class="form-group">
                        <label for="">Price</label>
                        <input name="price" type="text" class="form-control" value="" required>
                        @error('price')
                        <div class="help-block" role="alert">
                            <strong>{{ $message }}</strong>
                        </div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="">Description</label>
                        <textarea name="description" class="form-control"></textarea>
                        @error('description')
                        <div class="help-block" role="alert">
                            <strong>{{ $message }}</strong>
                        </div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    var modal = $('#itemModal');
    $('.js-edit-item').on('click', function(e) {
        e.preventDefault();
        $('#itemModal').modal('show');
        var item = $(this).closest('.card--item');
        modal.find("form").attr('action', '/truck/menu/item/' + item.find("[name='category_id']").val());
        modal.find("[name='name']").val(item.find("[name='name']").val());
        modal.find("[name='price']").val(item.find("[name='price']").val());
        modal.find("[name='category_id']").val(item.find("[name='category_id']").val());
        modal.find("[name='description']").val(item.find("[name='description']").val());
    });
</script>
@include('truck.layouts.client.footer')
