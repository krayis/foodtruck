@foreach (['danger', 'warning', 'success', 'info'] as $message)
    @if(Session::has($message))
        <p class="flash-alert alert alert-{{ $message }}">
            {{ Session::get($message) }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
        </p>
    @endif
@endforeach
