@include('truck.layouts.admin.head')
@include('truck.layouts.admin.nav')
@include('truck.settings._partials.subnav')

<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <div class="page-header">
                <h1>Payments</h1>
            </div>
            <form
                action="{{ $user->paymentProvider()->exists() ? route('truck.settings.payments.update', $user->paymentProvider->id) : route('truck.settings.payments.store') }}"
                method="POST">
                @csrf
                @if($user->paymentProvider()->exists())
                    @method('PATCH')
                @endif
                <label class="mb-0">Payment provider for customer payments</label>
                <div class="form-group @error('payment_provider') has-error @enderror">
                    <div class="radio">
                        <label>
                            <input type="radio" name="payment_provider" value="paypal" data-label="PayPal" {{ !$user->paymentProvider()->exists() || ($user->paymentProvider()->exists() && $user->paymentProvider->payment_provider === 'paypal') ? 'checked' : null }} />
                            Paypal
                        </label>
                    </div>
                    <div class="radio">
                        <label>
                            <input type="radio" name="payment_provider" value="stripe" data-label="Stripe" {{ $user->paymentProvider()->exists() && $user->paymentProvider->payment_provider === 'stripe' ? 'checked' : null }} />
                            Stripe
                        </label>
                    </div>
                    <div class="radio">
                        <label>
                            <input type="radio" name="payment_provider" value="square" data-label="Square" {{ $user->paymentProvider()->exists() && $user->paymentProvider->payment_provider === 'square' ? 'checked' : null }} />
                            Square
                        </label>
                    </div>
                    @error('payment_provider')
                    <div class="help-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </div>
                    @enderror
                </div>
                <div class="form-group @error('email') has-error @enderror">
                    <label for="">
                        <span id="js-payment-provider">
                            @if(!$user->paymentProvider()->exists() || ($user->paymentProvider()->exists() && $user->paymentProvider->payment_provider === 'paypal'))
                                PayPal
                            @elseif($user->paymentProvider()->exists() && $user->paymentProvider->payment_provider === 'stripe')
                                Stripe
                            @elseif ($user->paymentProvider()->exists() && $user->paymentProvider->payment_provider === 'square')
                                Sqaure
                            @endif
                        </span> account (email)
                    </label>
                    <input type="email" class="form-control" name="email" value="{{ $user->paymentProvider()->exists() ? $user->paymentProvider->email : null }}" required />
                    @error('email')
                    <div class="help-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </div>
                    @enderror
                </div>
                <div class="form-group @error('minimum_order_amount') has-error @enderror">
                    <label for="">Minimum order amount</label>
                    <input type="text" class="form-control" name="minimum_order_amount" value="{{ $user->paymentProvider()->exists() ? $user->paymentProvider->minimum_order_amount : null }}" required />
                    @error('minimum_order_amount')
                    <div class="help-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </div>
                    @enderror
                </div>
                <div class="form-group @error('sales_tax') has-error @enderror">
                    <label for="">Sales tax percentage</label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="sales_tax" value="{{ $user->paymentProvider()->exists() ? $user->paymentProvider->sales_tax : null }}" />
                        <span class="input-group-addon">%</span>
                    </div>
                    @error('sales_tax')
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
    var $paymentProviders = $('[name="payment_provider"]');
    $paymentProviders.on('change', function() {
        var $checked = $paymentProviders.filter(":checked");
        $('[name="email"]').val('');
        $('#js-payment-provider').text($checked.data('label'));
    });
</script>
@include('truck.layouts.admin.footer')
