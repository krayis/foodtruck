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
                    <button type="button" class="btn btn-grey" data-action="delete" data-target="delete-form">Delete
                    </button>
                    <button class="btn btn-primary">Save</button>
                </div>
            </div>
            <div class="form-group--title  @error('place_id') has-error @enderror">
                <input id="input-address" type="text" class="form-control" name="address" placeholder="Address"
                       value="{{ $location->formatted_address }}" readonly/>
                <input type="hidden" name="place_id" {{ $location->place_id }}/>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="form-group @error('note') has-error @enderror">
                    <label for="input-note">Location note</label>
                    <textarea id="input-note" class="form-control" rows="2" name="note">{{ $location->note }}</textarea>
                    @error('note')
                    <div class="help-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </div>
                    @enderror
                </div>
                <div class="form-group">
                    <div id="map" class="map" style="height: 500px; border-radius: 2px; overflow: hidden"></div>
                </div>
            </div>
        </div>


    </form>
    <script src="https://maps.googleapis.com/maps/api/js?key={{config('app.google_api_key')}}&callback=initMap"
            async></script>
    <script>
        var map,
            marker,
            $map = $('#map-view');

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

            updateMap({
                lat: {{ $location->latitude }},
                lng: {{ $location->longitude }},
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
