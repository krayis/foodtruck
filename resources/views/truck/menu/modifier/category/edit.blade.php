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
        <h1>Update Modifier Category</h1>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <form action="{{ route('truck.menu.modifier.category.update', $category->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PATCH')
                <div class="form-group @error('name') has-error @enderror">
                    <label for="">Modifier Category Name</label>
                    <input name="name" type="text" class="form-control" value="{{ $category->name }}" required>
                    @error('name')
                    <div class="help-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </div>
                    @enderror
                </div>
                <div class="form-group">
                    <div class="radio">
                        <label>
                            <input type="radio" name="modifier_category_type_id" value="1" {{ $category->modifier_category_type_id === 1 ? 'checked' : '' }} />
                            Require modifier selection (default)
                        </label>
                    </div>
                    <div class="radio">
                        <label>
                            <input type="radio" name="modifier_category_type_id" value="2" {{ $category->modifier_category_type_id === 2 ? 'checked' : '' }}/>
                            Require specific modifier selection
                        </label>
                    </div>
                    @error('modifier_category_type_id')
                    <div class="help-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </div>
                    @enderror
                </div>
                <div id="category-meta" style="display: {{ $category->modifier_category_type_id === 2 ? 'block' : 'none' }}">
                    <div class="row">

                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="">Minimum</label>
                                <select class="form-control js-quantity-selection" name="min">
                                    <option value="0">No Minimum</option>
                                    @for($i = 1; $i<=10; $i++)
                                        <option value="{{ $i }}" {{ $category->min === $i ? 'selected' : '' }}>{{ $i }}</option>
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
                                    <option value="0">No maximal</option>
                                    @for($i = 1; $i<=50; $i++)
                                        <option value="{{ $i }}" {{ $category->max === $i ? 'selected' : '' }}>{{ $i }}</option>
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
                    <div class="well">
                        <div class="modifier-limits" id="category-0-0" style="display: {{$category->min === 0 && $category->max === 0 ? 'block' : 'none'}};">Customer many select any number of modifiers.</div>
                        <div class="modifier-limits" id="category-limits" style="display: {{$category->min > 0 || $category->max > 0 ? 'block' : 'none'}};">
                            Customer must select a minimum of <strong id="js-min">{{$category->min}}</strong> and maximal of <strong id="js-max">{{$category->max}}</strong> item modifiers.
                        </div>
                    </div>
                </div>
                <button class="btn btn-primary">Save</button>
            </form>
        </div>
    </div>
</div>
<script>
    var typeInputs = $('[name="modifier_category_type_id"]');
    typeInputs.on('change', function () {
        $('#category-meta').css('display', 'none');
        if (typeInputs.filter(":checked").val() == 2) {
            $('#category-meta').css('display', 'block');
        }
    });

    var quantityInputs = $('.js-quantity-selection');
    quantityInputs.on('change', function () {
        $('.modifier-limits').css('display', 'none');
        var min = $('[name="min"]').find(':selected').val();
        var max = $('[name="max"]').find(':selected').val();
        if (min == 0 && max == 0) {
            $('#category-' + min + '-' + max).show();
        } else {
            $('#category-limits').show();
            $('#js-min').text(min);
            $('#js-max').text(max);
        }
    });
</script>
@include('truck.layouts.client.footer')
