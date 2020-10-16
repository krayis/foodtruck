@extends('truck.layouts.admin.layout')

@section('content')


    @foreach (['danger', 'warning', 'success', 'info'] as $message)
        @if(Session::has($message))
            <p class="alert alert-{{ $message }}">{{ Session::get($message) }}</p>
        @endif
    @endforeach

    <form action="{{ route('truck.location.destroy', $location->id) }}" style="display: none;" id="delete-form"
          method="POST">
        @csrf
        @method('DELETE')
    </form>

    <form action="{{ route('truck.location.update', $location->id) }}" method="POST">
        @csrf
        @method('PATCH')

        <div class="meta-header">
            <div class="meta-inner">
                <a href="{{ route('truck.location.index') }}" class="back">
                    <ion-icon name="arrow-back"></ion-icon>
                </a>
                <div class="meta-buttons">
                    <button type="button" class="btn btn-default" data-action="delete" data-target="delete-form">Delete
                    </button>
                    <button class="btn btn-primary">Save</button>
                </div>
            </div>
            <div class="form-group--title  @error('place_id') has-error @enderror">
                <input id="input-address" type="text" class="form-control" name="address" placeholder="Address"
                       value="{{ $location->formatted_address }}"/>
                <input type="hidden" name="place_id" {{ $location->place_id }}/>
                @error('place_id')
                <div class="help-block" role="alert">
                    <strong>Please make sure you have a confirmed address.</strong>
                </div>
                @enderror
            </div>
        </div>

        <div class="row">
            <div class="col-sm-15">
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
            </div>
        </div>
    </form>
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

        $('[data-action="delete"]').on('click', function (e) {
            e.preventDefault();
            if (confirm('Are you sure you want to delete?')) {
                $('#' + $(this).data('target')).submit();
            }
        });
    </script>

@endsection
