@extends('merchant.layouts.admin.layout')

@section('content')
@include('merchant.settings._partials.subnav')
<form action="{{ route('merchant.settings.coupons.update', $coupon->id) }}" method="POST">
    @csrf
    @method('PATCH')
<div class="meta-header">
    <div class="meta-inner">
        <a href="{{ route('merchant.settings.coupons.index') }}" class="back">
            <ion-icon name="arrow-back"></ion-icon>
        </a>
        <div class="meta-buttons">
            <button type="button" class="btn btn-grey" data-action="delete" data-target="delete-form">Delete
            </button>
            <button class="btn btn-primary">Save</button>
        </div>
    </div>
    <div class="form-group--title @error('code') has-error @enderror">
        <input type="text" class="form-control" name="code" value="{{ $coupon->code }}" placeholder="Coupon code" required/>
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
                    <input type="text" class="form-control" name="description" value="{{ $coupon->description }}"/>
                    @error('description')
                    <div class="help-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </div>
                    @enderror
                </div>
                <div class="form-group @error('min') has-error @enderror">
                    <label for="">Minimum grand total</label>
                    <input type="text" class="form-control" placeholder="0.00" name="min" value="{{ $coupon->min }}" required/>
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
                               value="0" {{ $coupon->type == 0  ? 'checked' : null }}> Flat discount
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="type" value="1" {{ $coupon->type == 1  ? 'checked' : null }}>
                        Percentage discount
                    </label>
                </div>
                <div class="form-group form-group__discount @error('discount_amount') has-error @enderror" id="discount-0"
                     style="display: {{ $coupon->type == 0  ? 'block' : 'none' }}">
                    <label for="">Discount amount</label>
                    <input type="text" class="form-control" name="discount_amount"
                           value="{{ $coupon->type == 0 ? $coupon->amount : null }}" {{ $coupon->type == 0 ? 'required' : null }} />
                    @error('discount_amount')
                    <div class="help-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </div>
                    @enderror
                </div>
                <div class="form-group form-group__discount @error('discount_percentage') has-error @enderror" id="discount-1"
                     style="display: {{ $coupon->type == 1 ? 'block' : 'none' }}">
                    <label for="">Discount percentage</label>
                    <input type="text" class="form-control" name="discount_percentage"
                           value="{{ $coupon->type == 1 ? $coupon->amount : null }}" {{ $coupon->type == 1 ? 'required' : null }} />
                    @error('discount_percentage')
                    <div class="help-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </div>
                    @enderror
                </div>
        </div>
    </div>
</form>
<form action="{{ route('merchant.settings.coupons.destroy',  $coupon->id) }}" id="delete-form"
      style="display: none;"
      method="POST">
    @csrf
    @method('DELETE')
</form>
<script>
    var typeInputs = $('[name="type"]');
    typeInputs.on('change', function () {
        $('.form-group__discount').css('display', 'none');
        $('.form-group__discount').find('input').prop('required', false);
        $('#discount-' + typeInputs.filter(":checked").val()).css('display', 'block');
        $('#discount-' + typeInputs.filter(":checked").val()).find('input').prop('required', true);

    });

    $('[data-action="delete"]').on('click', function (e) {
        e.preventDefault();
        if (confirm('Are you sure you want to delete?')) {
            $('#' + $(this).data('target')).submit();
        }
    });
</script>
@endsection
