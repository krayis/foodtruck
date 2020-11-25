<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Ensures optimal rendering on mobile devices. -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/> <!-- Optimal Internet Explorer compatibility -->
</head>

<body>
<script
    src="https://www.paypal.com/sdk/js?client-id=AZz5RtCxIUI3ComsW9nJobrvr1mJzGOGIPxquYKSGQsuxRusL9s-nko20mJ6pW27mtJyz48wFDAr_Nz9"> // Required. Replace SB_CLIENT_ID with your sandbox client ID.
</script>
<div id="paypal-button-container"></div>
<script>
    paypal.Buttons({
        createOrder: function (data, actions) {
            // This function sets up the details of the transaction, including the amount and line item details.
            return actions.order.create({
                purchase_units: [{
                    amount: {
                        value: '500'
                    },
                    // payee: {
                    //     email_address: 'payee@email.com' // REPLACE EMAIL ADDRESS
                    // }
                }]
            });
        },
        onApprove: function (data, actions) { // https://developer.paypal.com/docs/api/orders/v2/#orders_capture
            // This function captures the funds from the transaction.
            return actions.order.capture().then(function (details) {
                // This function shows a transaction success message to your buyer.
                // alert('Transaction completed by ' + details.payer.name.given_name);
            });
        }
    }).render('#paypal-button-container');
</script>
</body>
