@extends('truck.layouts.admin.layout')

@section('content')
@include('truck.settings._partials.subnav')


    <div class="page-header">
        <h1 class="mt-0">Order Throttling</h1>
    </div>
    <div class="row">
        <div class="col-sm-24 col-md-12">
            <p>Throttle orders to control how many can come in a certain time period.</p>
            <form action="{{ route('truck.settings.throttle.update', $user->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="form-group">
                    <div class="radio">
                        <label>
                            <input type="radio" name="throttle_type" value="0" {{ $user->truck->throttle_type == 0 ? 'checked' : null }} />
                            No throttling
                        </label>
                    </div>
                    <div class="radio">
                        <label>
                            <input type="radio" name="throttle_type" value="1" {{ $user->truck->throttle_type == 1 ? 'checked' : null }}>
                            Set a maximum # of orders that can be scheduled within a time slot
                        </label>
                    </div>
                    <div class="radio">
                        <label>
                            <input type="radio" name="throttle_type" value="2" {{ $user->truck->throttle_type == 2 ? 'checked' : null }}>
                            Set a maximum # of orders that can be scheduled within a time slot but as orders are
                            marked completed allow for orders
                        </label>
                    </div>
                    <div class="radio">
                        <label>
                            <input type="radio" name="throttle_type" value="3" {{ $user->truck->throttle_type == 3 ? 'checked' : null }}>
                            Set a maximum # of open orders but as orders are marked completed allow for orders
                        </label>
                    </div>
                    @error('throttle_type')
                    <div class="help-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </div>
                    @enderror
                </div>
                <div class="row">
                    <div class="col-sm-12 js-toggle-view" id="view-time-slot" style="display: {{ in_array($user->truck->throttle_type, [1,2]) ? 'block' : 'none' }}">
                        <div class="form-group">
                            <label for="">Time slot in minutes</label>
                            <select class="form-control" name="throttle_time_slot">
                                @for($i = 15; $i<=60; $i++)
                                    <option value="{{ $i }}" {{ $user->truck->throttle_time_slot == $i ? 'selected' : null }}>{{ $i }}</option>
                                @endfor
                            </select>
                            @error('throttle_time_slot')
                            <div class="help-block" role="alert">
                                <strong>{{ $message }}</strong>
                            </div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-sm-12 js-toggle-view" id="view-max-orders" style="display: {{ in_array($user->truck->throttle_type, [1,2,3]) ? 'block' : 'none' }}">
                        <div class="form-group">
                            <label for="">Maximum # of orders</label>
                            <select class="form-control" name="throttle_max_orders">
                                @for($i = 1; $i<=100; $i++)
                                    <option value="{{ $i }}" {{ $user->truck->throttle_max_orders == $i ? 'selected' : null }}>{{ $i }}</option>
                                @endfor
                            </select>
                            @error('throttle_max_orders')
                            <div class="help-block" role="alert">
                                <strong>{{ $message }}</strong>
                            </div>
                            @enderror
                        </div>
                    </div>
                </div>

                <button class="btn btn-primary">Save</button>
            </form>
        </div>
    </div>

<script>
    var typeInputs = $('[name="throttle_type"]');
    var $timeSlot = $('#view-time-slot');
    var $maxOrders = $('#view-max-orders');
    typeInputs.on('change', function () {
        $('.js-toggle-view').css('display', 'none');
        $('.js-toggle-view').find('select').prop('required', false);
        var $checked = typeInputs.filter(":checked");
        if ($checked.val() == 1 || $checked.val() == 2) {
            $timeSlot.css('display', 'block');
            $timeSlot.find('select').prop('required', true);
            $maxOrders.css('display', 'block');
            $maxOrders.find('select').prop('required', true);
        }
        if ($checked.val() == 3) {
            $maxOrders.css('display', 'block');
            $maxOrders.find('select').prop('required', true);
        }
    });
</script>
@endsection
