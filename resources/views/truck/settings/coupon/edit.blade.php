@include('truck.layouts.admin.head')
@include('truck.layouts.admin.nav')
@include('truck.settings._partials.subnav')
<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <h1>Edit Coupon</h1>
            <form action="{{ route('truck.settings.coupons.update', $coupon->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="form-group @error('code') has-error @enderror">
                    <label for="">Coupon code</label>
                    <input type="text" class="form-control" name="code" value="{{ $coupon->code }}" required/>
                    @error('code')
                    <div class="help-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </div>
                    @enderror
                </div>
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
                <button class="btn btn-primary">Save</button>
            </form>
        </div>
    </div>
</div>
<script>
    var typeInputs = $('[name="type"]');
    typeInputs.on('change', function () {
        $('.form-group__discount').css('display', 'none');
        $('.form-group__discount').find('input').prop('required', false);
        $('#discount-' + typeInputs.filter(":checked").val()).css('display', 'block');
        $('#discount-' + typeInputs.filter(":checked").val()).find('input').prop('required', true);

    });
</script>
@include('truck.layouts.admin.footer')
