@include('merchant.layouts.client.head', ['bodyClass' => 'login-page'])

<div class="card">
    <div class="flash-message">
        @foreach (['danger', 'warning', 'success', 'info'] as $message)
            @if(Session::has($message))
                <p class="alert alert-{{ $message }}">{{ Session::get($message) }}</p>
            @endif
        @endforeach
    </div>
    <form action="{{ route('merchant.login.store') }}" method="POST">
        @csrf
        <div class="card-header">FoodTruck Login</div>
        <div class="form-group @error('email') has-error @enderror">
            <label>Email</label>
            <input class="form-control" name="email" value="{{ old('email') }}" required/>
            @error('email')
            <div class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </div>
            @enderror
        </div>
        <div class="form-group @error('password') has-error @enderror">
            <label>Password</label>
            <input class="form-control" name="password" type="password" value="" required/>
            @error('password')
            <div class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </div>
            @enderror
        </div>
        <button class="btn btn-md btn-block btn-primary">Sign In</button>
    </form>
</div>

@include('merchant.layouts.client.footer')
