@include('layouts.head')
<div class="navbar">
    <h1 class="brand"><a href="{{ url('/') }}">Food<span>Truck</span>.am</a></h1>
    <div class="location-wrapper">
        <div class="location-wrapper-inner" data-action="search">
            <span class="address"><i class="icon ion-ios-pin"></i> <span id="address-preview">Enter an address</span> <i class="icon ion-ios-arrow-down"></i><i class="icon ion-ios-arrow-up"></i></span>
        </div>
        <div class="dropdown">
            <div class="dropdown-inner">
                <i class="icon ion-ios-pin"></i>
                <input type="text" placeholder="Enter an address" name="address" />
                <i class="icon ion-ios-close" data-action="clear-search" style="display: none"></i>
                <input type="hidden" name="place_id" />
            </div>
        </div>
    </div>
    <div class="search-wrapper">
        <div class="search-wrapper-inner">
            <span class="search-icon"><i class="icon ion-ios-search"></i></span>
            <input type="text" placeholder="Food truck name or ID"/>
        </div>
    </div>
</div>

<div class="container--index">
    <div class="header">
        <div class="topbar">
            <h1>25 results found</h1>
            <form class="form-inline">
                <label>Distance: </label>
                <select>
                    <option value="5">5 miles</option>
                    <option value="10">10 miles</option>
                    <option value="25">25 miles</option>
                    <option value="50">50 miles</option>
                    <option value="100">100 miles</option>
                </select>
            </form>
        </div>
    </div>
    <ul class="truck-list">
        @for($i=0; $i<=10; $i++)
            <li>
                <a href="#">
                    <div class="featured-image">
                        <div class="image-wrapper">
                            <div class="image"
                                 style="background-image: url('https://media-cdn.grubhub.com/image/upload/d_search:browse-images:default.jpg/w_460,h_127,q_auto,dpr_auto,c_fill,f_auto/rcorhmol9qmor2ceyxuu')"></div>
                        </div>
                        <div class="image-wrapper">
                            <div class="image"
                                 style="background-image: url('https://media-cdn.grubhub.com/image/upload/d_search:browse-images:default.jpg/w_460,h_127,q_auto,dpr_auto,c_fill,f_auto/rcorhmol9qmor2ceyxuu')"></div>
                        </div>
                    </div>
                    <div class="truck-name">Son of a Butcher</div>
                    <div class="meta">
                        <span class="left">Serving until 5:30am</span>
                        <span class="right">5 miles away</span>
                    </div>
                </a>
            </li>
        @endfor
    </ul>
</div>
<script>
    var $address = $('[name="address"]');

    $('body').on('click', function(e) {
        var $target = $(e.target);
        if ($target.closest('.location-wrapper').length === 0) {
            $('.location-wrapper').removeClass('active');
            $address.val('');
        }
    });

    $address.on('keyup', function() {
        if ($address.val().length  === 0) {
            $('[data-action="clear-search"]').css('display', 'none');
        }  else {
            $('[data-action="clear-search"]').css('display', 'block');
        }
    });

    $('[data-action="search"]').on('click', function() {
        $('.location-wrapper').toggleClass('active');
        $address.focus();
        $address.val('');
    });

    $('[data-action="clear-search"]').on('click', function() {
        $address.val('').focus();
        $address.trigger('keyup');
    });

    $address.autocomplete({
        appendTo: $address.parent(),
        source: function (request, response) {
            $.getJSON('{{ route('search@suggestions') }}', {
                term: request.term
            }, response);
        },
        focus: function (event, ui) {
            event.preventDefault();
        },
        select: function (event, ui) {
            event.preventDefault();
            $address.val(ui.item.label);
            $('#address-preview').text(ui.item.label);
            $('[name="place_id"]').val(ui.item.value);
            console.log(ui.item)
        },
    });
</script>
@include('layouts.footer')
