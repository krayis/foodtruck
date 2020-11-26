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
                <input id="input-address" type="text" class="form-control" name="address" placeholder="Address"
                       autocomplete="off"/>
                @error('place_id')
                <div class="help-block" role="alert">
                    <strong>Please make sure you have a confirmed address.</strong>
                </div>
                @enderror
            </div>
        </div>


        <div class="row">
            <div class="col-sm-12">
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
                <div class="form-group" id="js-map-container" style="display: none;">
                    <div id="map" class="map" rows="2"
                         style="height: 500px; border-radius: 3px; overflow: hidden"></div>
                </div>
                <input type="hidden" name="place_id"/>
            </div>
        </div>
    </form>

    <script
        src="https://maps.googleapis.com/maps/api/js?key={{config('app.google_api_key')}}&callback=initMap&libraries=places"
        async></script>

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
                    updateMapByPlaceId(ui.item.value);
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

    <script>
        var map,
            marker,
            $map = $('#map-view');


        function updateMap(cords) {
            map.setCenter(cords);
            marker.setPosition(cords);
        }


        function updateMapByPlaceId(placeId) {
            const geocoder = new google.maps.Geocoder();
            geocoder.geocode({placeId: placeId}, (results, status) => {
                map.setZoom(11);
                map.setCenter(results[0].geometry.location);

                // Set the position of the marker using the place ID and location.
                // @ts-ignore TODO(jpoehnelt) This should be in @typings/googlemaps.
                marker.setPlace({
                    placeId: placeId,
                    location: results[0].geometry.location,
                });

                marker.setVisible(true);

                infowindowContent.children["place-name"].textContent = place.name;
                infowindowContent.children["place-id"].textContent = place.place_id;
                infowindowContent.children["place-address"].textContent =
                    results[0].formatted_address;

                infowindow.open(map, marker);
            });
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
        }

        $('[data-action="delete"]').on('click', function (e) {
            e.preventDefault();
            if (confirm('Are you sure you want to delete?')) {
                $('#' + $(this).data('target')).submit();
            }
        });
    </script>

@endsection
