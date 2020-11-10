<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Online food truck ordering"/>
    <title>{{ config('app.name', 'Laravel') }}</title>
    <script src="{{ asset('js/app.js') }}" defer></script>
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Alegreya+Sans:wght@500;700&family=Roboto:wght@400;500&display=swap"
        rel="stylesheet">
    <script src="https://unpkg.com/ionicons@5.0.0/dist/ionicons.js"></script>
    <link href="{{ asset('css/client/styles.css') }}" rel="stylesheet">
    <style>
        input:focus {
            outline: none;
        }

        .jumbo {
            width: 100%;
            height: 100%;
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center center;
            padding: 150px 0;
            background-image: url(images/food_bento_bg.jpg);
            position: relative;
        }

        .jumbo .inner {
            max-width: 600px;
            width: 100%;
            margin: 0 auto;
            text-align: center;
            position: relative;
            z-index: 100;
        }

        .jumbo .inner h1 {
            font-weight: 700;
            line-height: 64px;
            letter-spacing: -0.04ch;
            font-size: 64px;
            color: #ffffff;
            margin: 0 0 25px;
        }

        .jumbo .inner input {
            border: 2px solid white;
            border-radius: 24px;
            height: 48px;
            padding: 12px 24px;
            font-size: 16px;
            line-height: 1.5;
            font-weight: 500;
            color: rgb(25, 25, 25);
            width: 100%;
            max-width: 300px;
            display: block;
            margin: 0 auto;
        }

        .jumbo .inner input:focus {
            border: 2px solid rgba(0, 0, 0, .5);
            width: 100%;
        }

        .section {
            padding: 75px 0;
        }

        .section--footer {
            padding: 75px 30px 90px;
            background: #000000;
            color: rgba(255,255,255,1)
        }
        .section--footer a {
            color: rgba(255,255,255,1);
        }

        .section--footer .meta {
            margin-top: 30px;
            padding-top: 30px;
            border-top: 1px solid #ffffff;
        }
        .section--cta h3 {
            font-size: 32px;
            font-weight: 700;
            line-height: 40px;
            letter-spacing: -0.04ch;
            margin-top: 0;
        }
        .section--cta p {
            font-size: 18px;
            line-height: 24px;
            margin-bottom: 12px;
        }
        .section--cta a {
            font-weight: 700;
            line-height: 24px;
            letter-spacing: -0.04ch;
            font-size: 18px;
        }
    </style>
</head>
<body>
<div class="jumbo">
    <div class="inner">
        <h1>Your favorite food trucks, online ordering</h1>
        <input type="text" placeholder="Enter address"/>
    </div>
</div>
<div class="section section--cta">
    <div class="container">
        <div class="row">
            <div class="col-sm-8 text-center">
                <h3>Order Online</h3>
                <p>
                    Find local food trucks and order online.
                </p>
                <div><a href="#">Find a food truck</a></div>
            </div>
            <div class="col-sm-8 text-center">
                <h3>Become a Partner</h3>
                <p>
                    Grow your business and reach new customers by partnering with us.
                </p>
                <div>
                    <a href="/partner">Sign up your truck</a>
                </div>
            </div>
            <div class="col-sm-8 text-center">
                <h3>Try the App</h3>
                <p>
                    Get the best experience ordering online.
                </p>
                <div>
                    <a href="#">Get the app</a>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="section section--footer">
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                FoodTruck.am
            </div>
            <div class="col-xs-6">&nbsp;</div>
            <div class="col-xs-6">
                <a href="#">Get Help</a> <br />
                <a href="#">Read FAQs</a> <br />
            </div>

        </div>
    </div>
    <div class="text-right meta">
        © 2020 FoodTruck.am
    </div>
</div>
</body>
</html>
