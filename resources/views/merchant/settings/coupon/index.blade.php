@extends('merchant.layouts.admin.layout')

@section('content')
    @include('merchant.settings._partials.subnav')

    <div class="page-header">
        <h1 class="inline-block">Coupons</h1>
        <div class="page-action">
            <a class="btn btn-primary" href="{{ route('merchant.settings.coupons.create') }}"><ion-icon name="add"></ion-icon> New Coupon</a>
        </div>
    </div>

    <table class="table">
        <thead>
        <tr>
            <th>Code</th>
            <th>Minimum Grand Total</th>
            <th>Amount</th>
            <th width="1%"></th>
        </tr>
        </thead>
        <tbody>
        @foreach($coupons as $coupon)
            <tr>
                <td><a href="{{ route('merchant.settings.coupons.edit',  $coupon->id) }}">{{ $coupon->code }}</a></td>
                <td>${{ $coupon->min }}</td>
                <td>
                    @if($coupon->type == 1)
                        {{ $coupon->amount }}%
                    @else
                        ${{ $coupon->amount }}
                    @endif
                </td>
                <td>
                    <form action="{{ route('merchant.settings.coupons.destroy',  $coupon->id) }}" class="item-delete"
                          style="display: none;"
                          method="POST">
                        @csrf
                        @method('DELETE')
                    </form>
                    <form action="{{ route('merchant.settings.coupons.update',  $coupon->id) }}"
                          class="item-toggle-state" style="display: none;" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="active" value="{{ $coupon->active === 1 ? 0 : 1 }}"/>
                    </form>
                    <div class="dropdown">
                        <a href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                            <ion-icon name="ios-more"></ion-icon>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenu1">
                            <li><a href="{{ route('merchant.settings.coupons.edit', $coupon->id ) }}">
                                    <ion-icon name="ios-create"></ion-icon>
                                    Edit</a></li>
                            <li><a href="#" class="js-table-toggle-state">
                                    <ion-icon
                                        name="{{ $coupon->active === 1 ? 'pause' : 'ios-radio-button-on' }}"></ion-icon> {{ $coupon->active === 1 ? 'Disable' : 'Enabled' }}
                                </a></li>
                            <li><a href="#" class="js-table-delete">
                                    <ion-icon name="ios-trash"></ion-icon>
                                    Delete</a></li>
                        </ul>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>


    <script>
        $('.js-table-delete').on('click', function (e) {
            e.preventDefault();
            if (confirm('Are you sure you want to delete?')) {
                $(this).closest('td').find('form.item-delete').submit();
            }
        });
        $('.js-table-toggle-state').on('click', function (e) {
            e.preventDefault();
            if (confirm('Are you sure you want to disable?')) {
                $(this).closest('td').find('form.item-toggle-state').submit();
            }
        });
    </script>
@endsection
