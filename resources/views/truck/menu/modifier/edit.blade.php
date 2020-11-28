@extends('truck.layouts.admin.layout')

@section('content')

@include('truck.menu._partials.navtabs')

<form action="{{ route('truck.menu.modifier.update', $modifier->id) }}" method="POST"
      enctype="multipart/form-data" autocomplete="off">
    @csrf
    @method('PATCH')

    <div class="meta-header">
        <div class="meta-inner">
            <a href="{{ route('truck.menu.modifier.index') }}" class="back">
                <ion-icon name="arrow-back"></ion-icon>
            </a>
            <div class="meta-buttons">
                <button type="button" class="btn btn-grey" data-action="delete" data-target="delete-form">Delete
                </button>
                <button class="btn btn-primary">Save</button>
            </div>
        </div>
        <div class="form-group--title  @error('name') has-error @enderror">
            <input name="name" type="text" class="form-control" value="{{ $modifier->name }}"
                   autocomplete="false" placeholder="Name" required>
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
                    <label for="">Modifier group</label>
                    <select name="modifier_group_id" class="form-control">
                        @foreach($categories as $category)
                            <option
                                value="{{ $category->id }}" {{$modifier->modifier_group_id == $category->id ? 'selected' : null}}>{{ $category->name }}</option>
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
                    <input name="price" type="text" class="form-control" value="{{ $modifier->price }}" required>
                    @error('price')
                    <div class="help-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </div>
                    @enderror
                </div>
                <div class="form-group @error('type') has-error @enderror">
                    <div><label>Item quantities</label></div>
                    <label class="radio-inline">
                        <input type="radio" name="type"
                               value="SINGLE" {{ $modifier->type === 'SINGLE' ? 'checked' : null }}> Single selection
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="type" value="MULTIPLE" {{ $modifier->type === 'MULTIPLE' ? 'checked' : null }}> Multiple
                        quantities
                    </label>
                    @error('type')
                    <div class="help-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </div>
                    @enderror
                    <input type="hidden" name="place_id" value="{{ old('place_id') }}"/>
                </div>
                <div id="multiple-view" style="display: {{ $modifier->type === 'MULTIPLE' ? 'block' : 'none' }}">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="">Minimum</label>
                                <select class="form-control js-quantity-selection" name="min">
                                    <option value="0">No Minimum</option>
                                    @for($i = 1; $i<=10; $i++)
                                        <option
                                            value="{{ $i }}" {{ $modifier->min == $i ? 'selected' : '' }}>{{ $i }}</option>
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
                                            value="{{ $i }}" {{ $modifier->max == $i ? 'selected' : '' }}>{{ $i }}</option>
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
<form action="{{ route('truck.menu.modifier.destroy', $modifier->id) }}" style="display: none;" id="delete-form"
      method="POST">
    @csrf
    @method('DELETE')
</form>

<script>
    var typeInputs = $('[name="type"]');
    typeInputs.on('change', function () {
        $('#multiple-view').css('display', 'none');
        if (typeInputs.filter(":checked").val() === 'MULTIPLE') {
            $('#multiple-view').css('display', 'block');
        }
    });
    $('[data-action="delete"]').on('click', function (e) {
        e.preventDefault();
        if (confirm('Are you sure you want to delete?')) {
            $('#' + $(this).data('target')).submit();
        }
    });
</script>
@endsection
