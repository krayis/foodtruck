@extends('merchant.layouts.admin.layout')

@section('content')

    @include('merchant.inventory._partials.navtabs')

    <form action="{{ route('merchant.inventory.sheets.store') }}" method="POST" enctype="multipart/form-data"
          autocomplete="off">
        @csrf
        <div class="meta-header">
            <div class="meta-inner">
                <a href="{{ route('merchant.inventory.sheets.index') }}" class="back">
                    <ion-icon name="arrow-back"></ion-icon>
                </a>
                <div class="meta-buttons">
                    <button class="btn btn-primary">Save</button>
                </div>
            </div>
            <div class="form-group form-group--title @error('name') has-error @enderror">
                <input name="name" type="text" class="form-control form-control" value="{{ old('name') }}"
                       autocomplete="false" placeholder="Name" required/>
                @error('name')
                <div class="help-block" role="alert">
                    <strong>{{ $message }}</strong>
                </div>
                @enderror
            </div>
        </div>

        <div class="row">
            <div class="col-md-16">
                <div class="form-group">
                    <label for="template">Assign to schedule event</label>
                    <select class="form-control" name="event_id">
                        @foreach($events as $event)
                            <option value="{{ $event->id }}">{{ $event->startDateTime($timezone)->format('g:ia m/d/y') }} to {{ $event->endDateTime($timezone)->format('g:ia m/d/y') }} at {{ $event->location->formatted_address }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
                <div class="col-md-5 col-sm-6">
                <div class="form-group">
                    <label for="template">Load from template</label>
                    <select class="form-control" name="template">
                        <option>--- Select template --- </option>
                        @foreach($templates as $row)
                            <option value="{{ $row->id }}">{{ $row->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <table class="table table-align-vertical">
            <thead>
            <tr>
                <th>Item</th>
                <th width="1%" class="white-space--nowrap">Quantity</th>
            </tr>
            </thead>
            <tbody>
            @foreach($items as $item)
                <tr class="@error('items.' .$item->id) has-error @enderror">
                    <td>{{ $item->name }}
                        @error('items.' .$item->id)
                        @foreach($errors->get('items.'.$item->id) as $error)
                            <p class="help-block">Please use a valid quantity.</p>
                        @endforeach
                        @enderror
                    </td>
                    <td>
                        <input type="text" name="items[{{$item->id}}]" data-id="{{$item->id}}"
                               class="form-control input-sm"
                               value="{{ old( 'items[0]', 0) }}"/>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </form>

    <script>
        var items = $('[name^="items"]');
        var template = @json($template)

        $('[name="template"]').on('change', function () {
            var value = $(this).find('option:selected').val();
            if (typeof template[value] !== 'undefined') {
                for (var i = 0; i < items.length; i++) {
                    var $item = $(items[i]);
                    var id = $item.data('id');
                    console.log(template[value]);
                    console.log(template[value])
                    if (typeof template[value][id] !== 'undefined') {
                        $item.val(template[value][id]);
                    } else {
                        $item.val(0);
                    }
                }
            }
        });
    </script>
@endsection
