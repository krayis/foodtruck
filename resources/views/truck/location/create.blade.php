@extends('truck.layouts.admin.layout')

@section('content')

    @foreach (['danger', 'warning', 'success', 'info'] as $message)
        @if(Session::has($message))
            <p class="alert alert-{{ $message }}">{{ Session::get($message) }}</p>
        @endif
    @endforeach

    <form action="{{ route('truck.location.store') }}" method="POST">
        @csrf
        <div class="meta-header">
            <div class="meta-inner">
                <a href="{{ route('truck.location.index') }}" class="back">
                    <ion-icon name="arrow-back"></ion-icon>
                </a>
                <div class="meta-buttons">
                    <button class="btn btn-primary">Save</button>
                </div>
            </div>
            <div class="form-group--title  @error('place_id') has-error @enderror">
                <input id="input-address" type="text" class="form-control" name="address" placeholder="Address" autocomplete="off"/>
                @error('place_id')
                <div class="help-block" role="alert">
                    <strong>Please make sure you have a confirmed address.</strong>
                </div>
                @enderror
            </div>
        </div>

        <div class="row">
            <div class="col-sm-15">
                <div class="form-group" id="js-preview-address" style="display: none">
                    <div class="well">
                        <strong>Confirmed Address:</strong>
                        <div id="js-preview-address-formatted"></div>
                    </div>
                </div>
                <div class="form-group @error('note') has-error @enderror">
                    <label for="input-note">Location note</label>
                    <textarea id="input-note" class="form-control" name="note">{{ old('note') }}</textarea>
                    @error('note')
                    <div class="help-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </div>
                    @enderror
                </div>
                <input type="hidden" name="place_id"/>
            </div>
        </div>

    </form>
    <script>
        $(document).ready(function () {
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
        });
    </script>

@endsection
