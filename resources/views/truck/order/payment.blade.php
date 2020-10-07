<!-- link to the SqPaymentForm library -->
<script type="text/javascript" src="https://js.squareupsandbox.com/v2/paymentform">
</script>

<style>/*
  Copyright 2019 Square Inc.
  Licensed under the Apache License, Version 2.0 (the "License");
  you may not use this file except in compliance with the License.
  You may obtain a copy of the License at
      http://www.apache.org/licenses/LICENSE-2.0
  Unless required by applicable law or agreed to in writing, software
  distributed under the License is distributed on an "AS IS" BASIS,
  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
  See the License for the specific language governing permissions and
  limitations under the License.
*/

    * {
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }

    body, html {
        background-color: #F7F8F9;
        color: #373F4A;
        font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
        font-weight: normal;
        height: 100%;
    }

    button {
        border: 0;
        font-weight: 500;
    }

    fieldset {
        margin: 0;
        padding: 0;
        border: 0;
    }

    #form-container {
        position: relative;
        width: 380px;
        margin: 0 auto;
        top: 50%;
        transform: translateY(-50%);
    }

    .third {
        float: left;
        width: calc((100% - 32px) / 3);
        padding: 0;
        margin: 0 16px 16px 0;
    }

    .third:last-of-type {
        margin-right: 0;
    }

    /* Define how SqPaymentForm iframes should look */
    .sq-input {
        height: 56px;
        box-sizing: border-box;
        border: 1px solid #E0E2E3;
        background-color: white;
        border-radius: 6px;
        display: inline-block;
        -webkit-transition: border-color .2s ease-in-out;
        -moz-transition: border-color .2s ease-in-out;
        -ms-transition: border-color .2s ease-in-out;
        transition: border-color .2s ease-in-out;
    }

    /* Define how SqPaymentForm iframes should look when they have focus */
    .sq-input--focus {
        border: 1px solid #4A90E2;
    }

    /* Define how SqPaymentForm iframes should look when they contain invalid values */
    .sq-input--error {
        border: 1px solid #E02F2F;
    }

    #sq-card-number {
        margin-bottom: 16px;
    }

    /* Customize the "Pay with Credit Card" button */
    .button-credit-card {
        width: 100%;
        height: 56px;
        margin-top: 10px;
        background: #4A90E2;
        border-radius: 6px;
        cursor: pointer;
        display: block;
        color: #FFFFFF;
        font-size: 16px;
        line-height: 24px;
        font-weight: 700;
        letter-spacing: 0;
        text-align: center;
        -webkit-transition: background .2s ease-in-out;
        -moz-transition: background .2s ease-in-out;
        -ms-transition: background .2s ease-in-out;
        transition: background .2s ease-in-out;
    }

    .button-credit-card:hover {
        background-color: #4281CB;
    }
</style>


<div id="form-container">
    <div id="sq-card-number"></div>
    <div class="third" id="sq-expiration-date"></div>
    <div class="third" id="sq-cvv"></div>
    <div class="third" id="sq-postal-code"></div>
    <button id="sq-creditcard" class="button-credit-card" onclick="onGetCardNonce(event)">Pay $1.00</button>
</div> <!-- end #form-container -->
<script type="text/javascript">
    // Create and initialize a payment form object
    const paymentForm = new SqPaymentForm({
        // Initialize the payment form elements

        //TODO: Replace with your sandbox application ID
        applicationId: "sandbox-sq0idb-wYy35rDIHTFpu_cSyXR3oQ",
        inputClass: 'sq-input',
        autoBuild: false,
        // Customize the CSS for SqPaymentForm iframe elements
        inputStyles: [{
            fontSize: '16px',
            lineHeight: '24px',
            padding: '16px',
            placeholderColor: '#a0a0a0',
            backgroundColor: 'transparent',
        }],
        // Initialize the credit card placeholders
        cardNumber: {
            elementId: 'sq-card-number',
            placeholder: 'Card Number'
        },
        cvv: {
            elementId: 'sq-cvv',
            placeholder: 'CVV'
        },
        expirationDate: {
            elementId: 'sq-expiration-date',
            placeholder: 'MM/YY'
        },
        postalCode: {
            elementId: 'sq-postal-code',
            placeholder: 'Postal'
        },
        // SqPaymentForm callback functions
        callbacks: {
            /*
            * callback function: cardNonceResponseReceived
            * Triggered when: SqPaymentForm completes a card nonce request
            */
            cardNonceResponseReceived: function (errors, nonce, cardData) {
                if (errors) {
                    // Log errors from nonce generation to the browser developer console.
                    console.error('Encountered errors:');
                    errors.forEach(function (error) {
                        console.error('  ' + error.message);
                    });
                    alert('Encountered errors, check browser developer console for more details');
                    return;
                }
                fetch('process-payment', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        nonce: nonce,
                        _token: '{{ csrf_token() }}'
                    })
                })
                    .catch(err => {
                        alert('Network error: ' + err);
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(errorInfo => Promise.reject(errorInfo)); //UPDATE HERE
                        }
                        return response.json(); //UPDATE HERE
                    })
                    .then(data => {
                        console.log(data); //UPDATE HERE
                        alert('Payment complete successfully!\nCheck browser developer console for more details');
                    })
                    .catch(err => {
                        console.error(err);
                        alert('Payment failed to complete!\nCheck browser developer console for more details');
                    });

            }
        }
    });
    paymentForm.build();
    // onGetCardNonce is triggered when the "Pay $1.00" button is clicked
    function onGetCardNonce(event) {
        // Don't submit the form until SqPaymentForm returns with a nonce
        event.preventDefault();
        // Request a nonce from the SqPaymentForm object
        paymentForm.requestCardNonce();
    }

</script>

