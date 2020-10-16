@extends('truck.layouts.admin.layout')

@section('content')

    @include('truck.menu._partials.navtabs')
    <form action="{{ route('truck.menu.modifier.store') }}" method="POST" enctype="multipart/form-data"
          autocomplete="off">
        @csrf

        <div class="meta-header">
            <div class="meta-inner">
                <a href="{{ route('truck.menu.modifier.index') }}" class="back">
                    <ion-icon name="arrow-back"></ion-icon>
                </a>
                <div class="meta-buttons">
                    <button class="btn btn-primary">Save</button>
                </div>
            </div>
            <div class="form-group--title  @error('name') has-error @enderror">
                <input name="name" type="text" class="form-control" placeholder="Name" value="{{ old('name') }}"
                       autocomplete="false" required>
                @error('name')
                <div class="help-block" role="alert">
                    <strong>{{ $message }}</strong>
                </div>
                @enderror
            </div>
        </div>


        <div class="row">
            <div class="col-sm-24 col-md-15">
                <div class="form-group @error('category_id') has-error @enderror">
                    <label for="">Modifier category</label>
                    <select name="modifier_group_id" class="form-control">
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('modifier_group_id')
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
                        <input type="radio" name="type"
                               value="0" {{ old('type') == '0' || !old('type') ? 'checked' : null }}> Single selection
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="type" value="1" {{ old('type') == '1' ? 'checked' : null }}> Multiple
                        quantities
                    </label>
                    <input type="hidden" name="place_id" value="{{ old('place_id') }}"/>
                </div>
                <div id="multiple-view" style="display: {{ old('type') == 1 ? 'block' : 'none' }}">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="">Minimum</label>
                                <select class="form-control js-quantity-selection" name="min">
                                    <option value="0">No Minimum</option>
                                    @for($i = 1; $i<=10; $i++)
                                        <option
                                            value="{{ $i }}" {{ old('min') == $i ? 'selected' : '' }}>{{ $i }}</option>
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
                                <label for="">Maximum</label>
                                <select class="form-control js-quantity-selection" name="max">
                                    @for($i = 1; $i<=50; $i++)
                                        <option
                                            value="{{ $i }}" {{ old('max') == $i ? 'selected' : '' }}>{{ $i }}</option>
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
            </div>
        </div>
    </form>
    <script>
        var typeInputs = $('[name="type"]');
        typeInputs.on('change', function () {
            $('#multiple-view').css('display', 'none');
            if (typeInputs.filter(":checked").val() == 1) {
                $('#multiple-view').css('display', 'block');
            }
        });
    </script>
@endsection
