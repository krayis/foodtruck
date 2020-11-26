@extends('truck.layouts.admin.layout')

@section('content')
    @foreach (['danger', 'warning', 'success', 'info'] as $message)
        @if(Session::has($message))
            <p class="alert alert-{{ $message }}">{{ Session::get($message) }}</p>
        @endif
    @endforeach
    <form action="{{ route('truck.schedule.store') }}" method="POST">
        <div class="meta-header">
            <div class="meta-inner">
                <a href="{{ route('truck.schedule.index') }}" class="back">
                    <ion-icon name="arrow-back"></ion-icon>
                </a>
                <div class="meta-buttons">
                    <button class="btn btn-primary">Save</button>
                </div>
            </div>
            <div class="form-group--title">
                <input type="text" class="form-control" name="date" placeholder="{{ date('m/d/Y') }}"
                       autocomplete="off" value="{{ old('date') }}" required>
                @error('date')
                <div class="help-block" role="alert">
                    <strong>{{ $message }}</strong>
                </div>
                @enderror
            </div>
        </div>
        <div class="row">
            <div class="col-md-16">
                @csrf
                <div class="form-group">
                    <div class="row">
                        <div class="col-sm-12">
                            <label>Start time</label>
                            <input type="text" class="form-control" name="start_time" placeholder="10:00 am"
                                   value="{{ old('start_time') }}"
                                   autocomplete="off" required>
                            @error('start_time')
                            <div class="help-block" role="alert">
                                <strong>{{ $message }}</strong>
                            </div>
                            @enderror
                        </div>
                        <div class="col-sm-12">
                            <label>End time</label>
                            <input type="text" class="form-control" name="end_time" placeholder="2:00 pm"
                                   value="{{ old('end_time') }}"
                                   autocomplete="off" required>
                            @error('end_time')
                            <div class="help-block" role="alert">
                                <strong>{{ $message }}</strong>
                            </div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="preorder" value="1" checked /> Allow pre-order
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <div><label>Address</label></div>
                    <label class="radio-inline">
                        <input type="radio" name="location"
                               value="save" {{ old('place_id') == 'save' || !old('place_id') ? 'checked' : null }}>
                        Saved location
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="location"
                               value="new" {{ old('place_id') == 'new' ? 'checked' : null }}> New location
                    </label>
                    <input type="hidden" name="place_id" value="{{ old('place_id') }}"/>
                </div>
                <div class="location-inputs" id="location-save">
                    <div class="form-group">
                        <select class="form-control" name="location_id">
                            @foreach($locations as $location)
                                <option value="{{ $location->id }}">
                                    {{ strpos($location->formatted_address, $location->name) === false ?  $location->name . ', ' . $location->formatted_address: $location->formatted_address }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div id="location-new" class="location-inputs" style="display: none;">
                    <div class="form-group @error('place_id') has-error @enderror">
                        <label for="input-address">Address</label>
                        <input autocomplete="off" id="input-address" type="text" class="form-control"
                               name="address" value="{{ old('address') }}"/>
                        @error('place_id')
                        <div class="help-block" role="alert">
                            <strong>Please make sure you have a confirmed address.</strong>
                        </div>
                        @enderror
                    </div>
                    <div class="form-group" id="js-preview-address" style="display: none">
                        <div class="well">
                            <strong>Confirmed address:</strong>
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
                </div>
            </div>
        </div>
    </form>
    <script>
        var radioInputs = $('[name="location"]');
        radioInputs.on('change', function () {
            $('.location-inputs').css('display', 'none');
            $('#location-' + radioInputs.filter(":checked").val()).css('display', 'block');
            radioInputs.filter(":checked").val() == 'new' ? $('[name="address_123456789"]').prop('required', true) : $('[name="address_123456789"]').prop('required', false);
        });
        $('[name="date"]').datepicker();
        $('[name="start_time"]').timepicker({
            timeFormat: 'hh:mm a'
        });
        $('[name="end_time"]').timepicker({
            timeFormat: 'hh:mm a'
        });
    </script>
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
                    $('#js-preview-address, #js-map-container').show();
                },
                focus: function (event, ui) {
                    event.preventDefault();
                    $(this).val(ui.item.label);
                    $('#js-preview-address-formatted').text(ui.item.label);
                    $('[name="place_id"]').val(ui.item.value);
                }
            });
        });
    </script>
@endsection
