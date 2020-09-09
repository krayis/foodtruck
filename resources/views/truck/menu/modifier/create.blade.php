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
        <h1>Create Modifier</h1>
    </div>

    <div class="row">
        <div class="col-sm-24 col-md-12">

            <form action="{{ route('truck.menu.modifier.store') }}" method="POST" enctype="multipart/form-data" autocomplete="off">
                @csrf
                <div class="form-group @error('name') has-error @enderror">
                    <label for="">Modifier name</label>
                    <input name="name" type="text" class="form-control" value="{{ old('name') }}" autocomplete="false" required>
                    @error('name')
                    <div class="help-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </div>
                    @enderror
                </div>
                <div class="form-group @error('category_id') has-error @enderror">
                    <label for="">Modifier category</label>
                    <select name="modifier_category_id" class="form-control">
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('modifier_category_id')
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
                <div class="form-group">
                    <div><label>Item quantities</label></div>
                    <label class="radio-inline">
                        <input type="radio" name="type" value="0" {{ old('type') == '0' || !old('type') ? 'checked' : null }}> Single selection
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="type" value="1" {{ old('type') == '1' ? 'checked' : null }}> Multiple quantities
                    </label>
                    <input type="hidden" name="place_id" value="{{ old('place_id') }}" />
                </div>
                <div id="multiple-view" style="display: {{ old('type') == 1 ? 'block' : 'none' }}">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="">Minimum</label>
                                <select class="form-control js-quantity-selection" name="min">
                                    <option value="0">No Minimum</option>
                                    @for($i = 1; $i<=10; $i++)
                                        <option value="{{ $i }}" {{ old('min') == $i ? 'selected' : '' }}>{{ $i }}</option>
                                    @endfor
                                </select>
                                @error('min')
                                <div class="help-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="">Maximal</label>
                                <select class="form-control js-quantity-selection" name="max">
                                    @for($i = 1; $i<=50; $i++)
                                        <option value="{{ $i }}" {{ old('max') == $i ? 'selected' : '' }}>{{ $i }}</option>
                                    @endfor
                                </select>
                                @error('max')
                                <div class="help-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <button class="btn btn-primary">Save</button>
            </form>
        </div>
    </div>
</div>
<script>
    var typeInputs = $('[name="type"]');
    typeInputs.on('change', function () {
        $('#multiple-view').css('display', 'none');
        if (typeInputs.filter(":checked").val() == 1) {
            $('#multiple-view').css('display', 'block');
        }
    });
</script>
@include('truck.layouts.client.footer')
