@include('truck.layouts.admin.head')
@include('truck.layouts.admin.nav')
<div class="container">
    <div class="page-header">
        <h1>Status</h1>
    </div>
    @if($event === null)
        <div class="alert alert-danger">
            <h3 class="mt-0">You are not accepting orders</h3>
            <p>Customers will not be able to place orders or find you in the search directory.</p>
        </div>
    @else
        <div class="alert alert-success">
            <h3 class="mt-0">You are currently accepting orders
                until {{ date('F jS', strtotime($event->end_date_time)) }}
                @ {{ date('g:ia', strtotime($event->end_date_time)) }}</h3>
            <p>If you need to change location you must go offline first.</p>
            <form class="mt-10" method="POST" action="{{ route('truck.status.update', $event->id) }}">
                @csrf
                @method('PATCH')
                <button class="btn btn-danger">Go Offline</button>
            </form>
        </div>
    @endif
    <div class="row">
        <div class="col-sm-12" style="display: {{$event === null ? 'block' : 'none'}}">
            <div class="page-header">
                <h1>Find Your Location</h1>
            </div>
            <form action="{{ route('truck.status.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="" class="mb-0">Address options</label>
                    <div class="radio">
                        <label>
                            <input type="radio" name="location_type_id"
                                   value="1" {{  old('location_type_id') == 1 || !old('location_type_id') ? 'checked' : null }} />
                            Saved location
                        </label>
                    </div>
                    <div class="radio">
                        <label>
                            <input type="radio" name="location_type_id"
                                   value="2" {{ old('location_type_id') == 2 ? 'checked' : null }} />
                            New address
                        </label>
                    </div>
                    <div class="radio">
                        <label>
                            <input type="radio" name="location_type_id"
                                   value="3" {{ old('location_type_id') == 3 ? 'checked' : null }} />
                            Find me
                        </label>
                    </div>
                </div>
                <div class="form-group form-group__toggle_view" id="view-1"
                     style="display: {{ old('location_type_id') == 1 || !old('location_type_id') ? 'block' : 'none' }}">
                    <label>Address</label>
                    <select name="location_id" class="form-control" required>
                        <option value="" disabled selected>(Select an address)</option>
                        @foreach($locations as $location)
                            @if($location->location_type_id === 1)
                                <option value="{{ $location->id}}">
                                    {{ strpos($location->formatted_address, $location->name) === false ?  $location->name . ', ' . $location->formatted_address: $location->formatted_address }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                    @error('location_id')
                    <div class="help-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </div>
                    @enderror
                </div>
                <div class="form-group form-group__toggle_view" id="view-2"
                     style="display: {{ old('location_type_id') == 2 ? 'block' : 'none' }}">
                    <div class="form-group @error('place_id') has-error @enderror">
                        <label for="input-address">Address</label>
                        <input id="input-address" type="text" class="form-control" name="address_123456789"
                               autocomplete="off"/>
                        <input type="hidden" name="place_id"/>
                        @error('place_id')
                        <div class="help-block" role="alert">
                            <strong>Please make sure you have a confirmed address.</strong>
                        </div>
                        @enderror
                    </div>
                    <div class="form-group" id="js-preview-address" style="display: none">
                        <div class="well">
                            <strong>Confirmed Address:</strong>
                            <div id="js-preview-address-formatted"></div>
                        </div>
                    </div>
                </div>
                <div class="form-group__toggle_view" id="view-3"
                     style="display: {{ old('location_type_id') == 3 ? 'block' : 'none' }}">
                    <div
                        class="form-group {{ $errors->has('latitude') || $errors->has('longitude') ? 'has-error' : null }}">
                        <input type="hidden" name="find_me_latitude"/>
                        <input type="hidden" name="find_me_longitude"/>
                        <button class="btn btn-default" type="button" data-action="find-me">Find me</button>
                        @if($errors->has('latitude') || $errors->has('longitude'))
                            <div class="help-block" role="alert">
                                <strong>Invalid latitude/longitude.</strong>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="form-group">
                    <label>Select last time order can be placed</label>
                    <select class="form-control" name="end_date_time">
                        @foreach($range as $time)
                            <option value="{{ date('Y-m-d H:i:s', $time) }}">
                                {{ date('D', $time) === $carbon->format('D') ? "Today, " . date("M d, g:ia", $time) : date("D, M d, g:ia", $time)  }}
                            </option>
                        @endforeach
                    </select>
                    @error('end_date_time')
                    <div class="help-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </div>
                    @enderror
                </div>
                <button class="btn btn-primary">Go Online</button>
            </form>
        </div>
        <div class="col-sm-{{$event === null ? '12' : '24'}}" id="map-view" style="display: none;">
            <div class="page-header">
                <h1>Your Location</h1>
            </div>
            <div id="map" class="map" style="height: 500px; border-radius: 3px; overflow: hidden"></div>
        </div>
    </div>
</div>
<input type="hidden" name="saved_location_id" value="{{ $event !== null ? $event->location->id : null }}"/>
<script>
    var locations = {!! $locations->toJson() !!};
    var $map = $('#map-view');
    var map = null;
    var marker = null;
    var dots = null;

    $('[name="location_id"]').on('change', function () {
        var locationId = $(this).children('option:selected').val();
        var location = locations.find(function (location) {
            return location.id == locationId
        });
        var cords = {lat: location.latitude, lng: location.longitude};
        updateMap(cords);
    });

    function updateMap(cords) {
        map.setCenter(cords);
        marker.setPosition(cords);
    }

    function initMap() {
        map = new google.maps.Map(document.getElementById('map'), {
            zoom: 14
        });
        map.addListener('center_changed', function () {
            $map.css('display', 'block');
        });
        marker = new google.maps.Marker({
            map: map,
            animation: google.maps.Animation.DROP,
        });

        @if($event !== null)
        updateMap({
            lat: {{ $event->location->latitude }},
            lng: {{ $event->location->longitude }},
        });
        @endif
    }

    var $locationRadio = $('[name="location_type_id"]');
    $locationRadio.on('change', function () {
        $('.form-group__toggle_view').css('display', 'none');
        var value = $locationRadio.filter(":checked").val();
        $('#view-' + value).css('display', 'block');
        $map.css('display', 'none');
        $('[name="location_id"]').prop('required', false);
        if (value == 1) {
            var locationId = $('[name="location_id"]').children('option:selected').val();
            var location = locations.find(function (location) {
                return location.id == locationId
            });
            var cords = {lat: location.latitude, lng: location.longitude};
            map.setCenter(cords);
            marker.setPosition(cords);
            $('[name="location_id"]').prop('required', true);
        }
    });

    $('[data-action="find-me"]').on('click', function () {
        var $self = $(this);

        function success(position) {
            $self.text('Find me').prop('disabled', false);
            var cords = {
                lat: position.coords.latitude,
                lng: position.coords.longitude,
            }
            map.setCenter(cords);
            marker.setPosition(cords);
            $('[name="find_me_latitude"]').val(cords.lat)
            $('[name="find_me_longitude"]').val(cords.lng)
        }

        function error(a) {
            $self.text('Find me').prop('disabled', false);
            alert('Geolocation is not supported by your browser')
        }

        if (!navigator.geolocation) {
            alert('Geolocation is not supported by your browser');
        } else {
            $self.text('Loading...').prop('disabled', true);
            setTimeout(function () {
                navigator.geolocation.getCurrentPosition(success, error);
            }, Math.floor(Math.random() * 750) + 250, this);
        }
    });

    $('[name="address_123456789"]').autocomplete({
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
<script src="https://maps.googleapis.com/maps/api/js?key={{config('app.google_api_key')}}&callback=initMap"
        async></script>
@include('truck.layouts.client.footer')
