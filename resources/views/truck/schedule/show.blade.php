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
        <h1>Update Date</h1>
    </div>
    <div class="row">
        <div class="col-sm-12">

            <form action="{{ route('truck.schedule.update', $schedule->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="form-group">
                    <label>Schedule date</label>
                    <input type="text" class="form-control" name="date" placeholder="{{ date('m/d/Y') }}"
                           autocomplete="off" value="{{ Date('m/d/Y', strtotime($schedule->start_date_time)) }}" required>
                    @error('date')
                    <div class="help-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </div>
                    @enderror
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-sm-12">
                            <label>Start time</label>
                            <input type="text" class="form-control" name="start_time" placeholder="10:00 am" value="{{ Date('h:i a', strtotime($schedule->start_date_time)) }}"
                                   autocomplete="off" required>
                            @error('start_time')
                            <div class="help-block" role="alert">
                                <strong>{{ $message }}</strong>
                            </div>
                            @enderror
                        </div>
                        <div class="col-sm-12">
                            <label>End time</label>
                            <input type="text" class="form-control" name="end_time" placeholder="2:00 pm" value="{{ Date('h:i a', strtotime($schedule->end_date_time)) }}"
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
                    <div><label>Address</label></div>
                    <label class="radio-inline">
                        <input type="radio" name="location" value="save" checked> Saved location
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="location" value="new"> New location
                    </label>
                    <input type="hidden" name="place_id" />
                </div>
                <div class="location-inputs" id="location-save">
                    <div class="form-group">
                        <select class="form-control" name="location_id">
                            @foreach($locations as $location)
                                <option value="{{ $location->id }}" {{ $schedule->location_id == $location->id ? 'checked' : null}}>
                                    {{ strpos($location->formatted_address, $location->name) === false ?  $location->name . ', ' . $location->formatted_address: $location->formatted_address }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div id="location-new" class="location-inputs" style="display: none">
                    <div class="form-group @error('place_id') has-error @enderror">
                        <label for="input-address">Address</label>
                        <input id="input-address" type="text" class="form-control" name="address_123456789" autocomplete="off" value="{{ old('address') }}"/>
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
                <button class="btn btn-primary">Save</button>
            </form>

        </div>
    </div>
</div>
<script>
    var radioInputs = $('[name="location"]');
    radioInputs.on('change', function () {
        $('.location-inputs').css('display', 'none');
        console.log(radioInputs.filter(":checked").val())
        $('#location-' + radioInputs.filter(":checked").val()).css('display', 'block');
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
        $('[name="address_123456789"]').autocomplete({
            source: function (request, response) {
                $.getJSON("/truck/location/search", {
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
@include('truck.layouts.client.footer')
