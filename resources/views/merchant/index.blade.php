@include('merchant.layouts.client.head')
@include('merchant.layouts.client.nav')

<div class="header">
    <div class="container">
        <div class="row">
            <div class="col-xs-24 col-sm-12">
                <h1>Increase your sales with online ordering
                    <small>Customer can now find you online and place orders</small>
                </h1>
            </div>
            <div class="col-xs-24 col-sm-12">
                <form action="{{ route('merchant.register.index') }}" method="GET">
                    <div class="card">
                        <div class="card-header">Start For Free!</div>
                        <div class="form-group">
                            <input class="form-control input-lg" placeholder="Food Truck Name" name="food_truck_name"
                                   required/>
                        </div>
                        <div class="form-group">
                            <input class="form-control input-lg" placeholder="Email Address" name="email" type="email"
                                   required/>
                        </div>
                        <div class="form-group">
                            <input class="form-control input-lg" placeholder="Mobile Phone" name="mobile_phone"
                                   required/>
                        </div>
                        <button class="btn btn-md btn-block btn-primary">Get Started</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="features">
    <div class="container">
        <h2 class="text-center">Core Features of the Foodmerchant.me Platform</h2>
        <ul class="feature-list">
            <li>
                <div class="card">
                    <h3>Online Ordering</h3>
                    <p>Allow your customers to place orders online and get ahead of the line.</p>
                </div>
            </li>
            <li>
                <div class="card">
                    <h3>Loyalty Program</h3>
                    <p>Give your customers a reward/credit for placing an order.</p>
                </div>
            </li>
            <li>
                <div class="card">
                    <h3>Coupon Code</h3>
                    <p>Give your customers a flat discount or percentage off their order.</p>
                </div>
            </li>
            <li>
                <div class="card">
                    <h3>Future Orders</h3>
                    <p>Allow your customer to place pre-orders for schedule dates.</p>
                </div>
            </li>
            <li>
                <div class="card">
                    <h3>Event Calendar</h3>
                    <p>Load your sharable calendar with times and places you will be attending.</p>
                </div>
            </li>
            <li>
                <div class="card">
                    <h3>Order Notification</h3>
                    <p>Get notified via email and/or sms text whenever an order comes through.</p>
                </div>
            </li>
        </ul>
    </div>
</div>
<div class="about">
    <div class="container">
        <div class="relative">
            <div class="brand">About Foodmerchant.me</div>
            <p>
                Foodmerchant.am helps you find and order food from wherever you are. How it works: you type in an address, we
                tell you the restaurants that deliver to that locale as well as showing you droves of pickup restaurants
                near you. Want to be more specific? Search by cuisine, restaurant name or menu item. We'll filter your
                results accordingly. When you find what you're looking for, you can place your order online or by phone,
                free of charge. Oh, and we also give you access to reviews, coupons, special deals and a 24/7 customer
                care team that tracks each order and makes sure you get exactly what you want.
            </p>
        </div>
    </div>
</div>
@include('merchant.layouts.client.footer')
