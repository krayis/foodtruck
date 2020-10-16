@extends('truck.layouts.admin.layout')

@section('content')

        @foreach (['danger', 'warning', 'success', 'info'] as $message)
            @if(Session::has($message))
                <p class="alert alert-{{ $message }}">{{ Session::get($message) }}</p>
            @endif
        @endforeach


    <div class="page-header">
        <h1>Orders</h1>

    </div>

        <table class="table">
            <thead>
            <tr>
                <th>Order #</th>
                <th>Name</th>
                <th>Items</th>
                <th>Pick up time</th>
                <th>Grand total</th>
            </tr>
            </thead>
            <tbody>
            @foreach($orders as $order)
                <tr class="order-list">
                    <td><a href="{{$order->id}}">{{$order->id}}</a></td>
                    <td>Jose DeLeon</td>
                    <td>
                        <table class="table table-condensed table-bordered">
                            <tbody>
                            @foreach($order->items as $item)
                                <tr>
                                    <td>
                                        {{$item->name}}
                                        @if($item->modifiers->count())
                                            <ul>
                                                @foreach($item->modifiers as $modifier)
                                                    <li>{{$modifier->name}}</li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </td>
                    <td>{{Date('n-j-Y', strtotime($order->pickup_at))}} <br/> {{Date('g:i', strtotime($order->pickup_at))}}-{{Date('g:ia', strtotime($order->pickup_at) + (15 * 60))}}</td>
                    <td>${{$order->grand_total}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>


@endsection
