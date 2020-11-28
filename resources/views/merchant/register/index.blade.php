@include('merchant.layouts.client.head', ['bodyClass' => 'register-page'])
<div class="card">
    <form action="{{ route('merchant.register.store') }}" method="POST">
        @csrf
        <div class="card-header">Tell us about your food truck</div>
        <div class="form-group @error('food_truck_name') has-error @enderror">
            <label>Food Truck Name</label>
            <input class="form-control" placeholder="Epic Hamburgers" name="food_truck_name"
                   value="{{ app('request')->input('food_truck_name')  ? app('request')->input('food_truck_name') : old('food_truck_name')  }}"
                   required/>
            @error('food_truck_name')
            <div class="help-block" role="alert">
                <strong>{{ $message }}</strong>
            </div>
            @enderror
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="form-group @error('first_name') has-error @enderror">
                    <label>First Name</label>
                    <input class="form-control" placeholder="John" name="first_name" value="{{ old('first_name') }}"
                           required/>
                    @error('first_name')
                    <div class="help-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </div>
                    @enderror
                </div>

            </div>
            <div class="col-sm-12">
                <div class="form-group @error('last_name') has-error @enderror">
                    <label>Last Name</label>
                    <input class="form-control" placeholder="Smith" name="last_name" value="{{ old('last_name') }}"
                           required/>
                    @error('last_name')
                    <div class="help-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </div>
                    @enderror
                </div>
            </div>
        </div>
        <div class="form-group @error('email') has-error @enderror">
            <label>Email Address</label>
            <input class="form-control" placeholder="Email Address" name="email"
                   value="{{ app('request')->input('email') }}" {{ old('email') }} type="email" required/>
            @error('email')
            <div class="help-block" role="alert">
                <strong>{{ $message }}</strong>
            </div>
            @enderror
        </div>
        <div class="form-group @error('mobile_phone') has-error @enderror">
            <label>Mobile Phone</label>
            <input class="form-control" placeholder="Mobile Phone" name="mobile_phone"
                   value="{{ app('request')->input('mobile_phone') }}" value="{{ old('mobile_phone') }}" required/>
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
                    <option value="{{ $key }}" {{ old('timezone') === $value ? 'selected' : null }}>{{ $value }}</option>
                @endforeach
            </select>
            @error('timezone')
            <div class="help-block" role="alert">
                <strong>{{ $message }}</strong>
            </div>
            @enderror
        </div>
        <div class="form-group">
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group @error('password') has-error @enderror">
                        <label>Password</label>
                        <input class="form-control" placeholder="Password" name="password" type="password" required/>
                        @error('password')
                        <div class="help-block" role="alert">
                            <strong>{{ $message }}</strong>
                        </div>
                        @enderror
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="form-group @error('password_confirmation') has-error @enderror">
                        <label>Confirm Password</label>
                        <input class="form-control" placeholder="Confirm Password" name="password_confirmation"
                               type="password" required/>
                    </div>
                    @error('password')
                    <div class="help-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </div>
                    @enderror
                </div>
            </div>
        </div>
        <button class="btn btn-md btn-block btn-primary">Create Account</button>
    </form>
</div>

@include('merchant.layouts.client.footer')
