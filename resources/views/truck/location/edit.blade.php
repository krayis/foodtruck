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
<div class="container">
    <div class="page-header">
        <h1>Update Location</h1>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <form action="{{ route('truck.location.update', $location->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="form-group @error('place_id') has-error @enderror">
                    <label for="input-address">Address</label>
                    <input id="input-address" type="text" class="form-control" name="address"
                           value="{{ $location->formatted_address }}"/>
                    <input type="hidden" name="place_id" {{ $location->place_id }}/>
                    @error('place_id')
                    <div class="help-block" role="alert">
                        <strong>Please make sure you have a confirmed address.</strong>
                    </div>
                    @enderror
                </div>
                <div class="form-group" id="js-preview-address">
                    <div class="well">
                        <strong>Confirmed Address:</strong>
                        <div id="js-preview-address-formatted">
                            {!! strpos($location->formatted_address, $location->name) === false ?  $location->name . '<br/>' . $location->formatted_address: $location->formatted_address !!}
                        </div>
                    </div>
                </div>
                <div class="form-group @error('note') has-error @enderror">
                    <label for="input-note">Location note</label>
                    <textarea id="input-note" class="form-control" name="note">{{ $location->note }}</textarea>
                    @error('note')
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
    $('[name="address"]').autocomplete({
        source: function (request, response) {
            $.getJSON('{{ route('truck.location@search') }}', {
                term: request.term
            }, response);
        },
        select: function (event, ui) {
            event.preventDefault();
            $(this).val(ui.item.label);
            $('#js-preview-address-formatted').text(ui.item.label);
            $('[name="place_id"]').val(ui.item.value);
            $('#js-preview-address').show();
        },
        focus: function (event, ui) {
            event.preventDefault();
            $(this).val(ui.item.label);
            $('#js-preview-address-formatted').text(ui.item.label);
            $('[name="place_id"]').val(ui.item.value);
            $('#js-preview-address').show();
        }
    });
</script>

@include('truck.layouts.client.footer')
