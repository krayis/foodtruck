@extends('truck.layouts.admin.layout')

@section('content')
@include('truck.settings._partials.subnav')
<form action="{{ route('truck.settings.coupons.store') }}" method="POST">
    @csrf
    <div class="meta-header">
        <div class="meta-inner">
            <a href="{{ route('truck.settings.coupons.index') }}" class="back">
                <ion-icon name="arrow-back"></ion-icon>
            </a>
            <div class="meta-buttons">
                <button class="btn btn-primary">Save</button>
            </div>
        </div>
        <div class="form-group--title">
            <input type="text" class="form-control" name="code" value="{{ old('code') }}" placeholder="Coupon code" required/>
            @error('code')
            <div class="help-block" role="alert">
                <strong>{{ $message }}</strong>
            </div>
            @enderror
        </div>
    </div>

    <div class="row">
        <div class="col-sm-24 col-md-15">
                <div class="form-group @error('description') has-error @enderror">
                    <label for="">Coupon description</label>
                    <input type="text" class="form-control" name="description" value="{{ old('description') }}"/>
                    @error('description')
                    <div class="help-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </div>
                    @enderror
                </div>
                <div class="form-group @error('min') has-error @enderror">
                    <label for="">Minimum grand total</label>
                    <input type="text" class="form-control" placeholder="0.00" name="min" value="{{ old('min') }}" required/>
                    @error('min')
                    <div class="help-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </div>
                    @enderror
                </div>
                <div class="form-group @error('type') has-error @enderror">
                    <div><label>Discount type</label></div>
                    <label class="radio-inline">
                        <input type="radio" name="type"
                               value="0" {{ old('type') == '0' || !old('type') ? 'checked' : null }}> Flat discount
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="type" value="1" {{ old('type') == '1' ? 'checked' : null }}>
                        Percentage discount
                    </label>
                </div>
                <div class="form-group form-group__discount @error('discount_amount') has-error @enderror" id="discount-0"
                     style="display: {{ old('type') == '0' || !old('type') ? 'block' : 'none' }}">
                    <label for="">Discount amount</label>
                    <input type="text" class="form-control" name="discount_amount"
                           value="{{ old('discount_amount') }}" {{ old('type') == '0' || !old('type') ? 'required' : null }} />
                    @error('discount_amount')
                    <div class="help-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </div>
                    @enderror
                </div>
                <div class="form-group form-group__discount @error('discount_percentage') has-error @enderror" id="discount-1"
                     style="display: {{ old('type') == 1 ? 'block' : 'none' }}">
                    <label for="">Discount percentage</label>
                    <input type="text" class="form-control" name="discount_percentage"
                           value="{{ old('discount_percentage') }}" {{ old('type') == 1 ? 'required' : null }} />
                    @error('discount_percentage')
                    <div class="help-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </div>
                    @enderror
                </div>
        </div>
    </div>
</form>

<script>
    var typeInputs = $('[name="type"]');
    typeInputs.on('change', function () {
        $('.form-group__discount').css('display', 'none');
        $('.form-group__discount').find('input').prop('required', false);
        $('#discount-' + typeInputs.filter(":checked").val()).css('display', 'block');
        $('#discount-' + typeInputs.filter(":checked").val()).find('input').prop('required', true);

    });
</script>
@endsection
