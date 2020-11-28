@extends('merchant.layouts.admin.layout')

@section('content')
@include('merchant.settings._partials.subnav')

    <div class="row">
        <div class="col-sm-24 col-md-12">
            <div class="page-header">
                <h1>Truck Settings</h1>
            </div>
            <form action="{{ route('merchant.settings.update', $user->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="form-group @error('name') has-error @enderror">
                    <label>Food truck name</label>
                    <input class="form-control" value="{{ $user->truck->name }}" name="name"
                           value="{{ app('request')->input('food_truck_name')  ? app('request')->input('food_truck_name') : old('food_truck_name')  }}"
                           required/>
                    @error('name')
                    <div class="help-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </div>
                    @enderror
                </div>

                <div class="form-group @error('email') has-error @enderror">
                    <label>Food truck email (will be display to customers)</label>
                    <input class="form-control" value="{{ $user->truck->email }}" name="email"
                           required/>
                    @error('email')
                    <div class="help-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </div>
                    @enderror
                </div>
                <div class="form-group @error('mobile_phone') has-error @enderror">
                    <label>Food truck phone (will be display to customers)</label>
                    <input class="form-control" placeholder="Mobile Phone" name="mobile_phone"
                           value="{{ $user->truck->mobile_phone }}"
                           required/>
                    @error('mobile_phone')
                    <div class="help-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </div>
                    @enderror
                </div>
                <div class="form-group @error('timezone') has-error @enderror">
                    <label>Timezone</label>
                    <select class="form-control" name="timezone">
                        @foreach($timezones as $key => $value)
                            <option value="{{ $key }}" {{ $user->timezone === $key ? 'selected' : null }}>{{ $value }}</option>
                        @endforeach
                    </select>
                    @error('timezone')
                    <div class="help-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </div>
                    @enderror
                </div>
                <button class="btn btn-primary">Save</button>
            </form>
        </div>
        <div class="col-sm-12">
            <div class="page-header">
                <h1>Change Password</h1>
            </div>
            <form action="{{ route('merchant.settings.password.update') }}" method="POST">
                @csrf
                @method("PATCH")
                <div class="form-group @error('current_password') has-error @enderror">
                    <label>Verify current password</label>
                    <input class="form-control" placeholder="Password" name="current_password" type="password"
                           required/>
                    @error('current_password')
                    <div class="help-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </div>
                    @enderror
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group @error('password') has-error @enderror">
                            <label>New Password</label>
                            <input class="form-control" placeholder="Password" name="password" type="password"
                                   required/>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group @error('password') has-error @enderror">
                            <label>Confirm new password</label>
                            <input id="password-confirm" type="password" class="form-control"
                                   name="password_confirmation" required autocomplete="new-password">
                        </div>
                    </div>
                    <div class="col-sm-24">
                        @error('password')
                        <div class="form-group @error('password') has-error @enderror">
                            <div class="help-block" role="alert">
                                <strong>{{ $message }}</strong>
                            </div>
                        </div>
                        @enderror
                    </div>
                </div>
                <button class="btn btn-primary">Save</button>
            </form>
        </div>
    </div>


@endsection
