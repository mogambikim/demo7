<?php

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    \Stripe\Stripe::setApiKey($config['stripe_secret_key']);
    
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    try {
        // Create a PaymentIntent with amount and currency
        $paymentIntent = \Stripe\PaymentIntent::create([
            'amount' => 1000, // amount in cents (e.g., $10.00)
            'currency' => $config['stripe_currency'],
            'payment_method' => $data['payment_method_id'],
            'confirmation_method' => 'manual',
            'confirm' => true,
        ]);
        
        // Respond with the payment intent status
        echo json_encode([
            'paymentIntent' => $paymentIntent,
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Stripe Payment Gateway Test</title>
    <script src="https://js.stripe.com/v3/"></script>
    <style>
        .StripeElement {
            box-sizing: border-box;
            height: 40px;
            padding: 10px 12px;
            border: 1px solid transparent;
            border-radius: 4px;
            background-color: white;
            box-shadow: 0 1px 3px 0 #e6ebf1;
            transition: box-shadow 150ms ease;
        }

        .StripeElement--focus {
            box-shadow: 0 1px 3px 0 #cfd7df;
        }

        .StripeElement--invalid {
            border-color: #fa755a;
        }

        .StripeElement--webkit-autofill {
            background-color: #fefde5 !important;
        }

        form {
            width: 400px;
            margin: 50px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            background-color: #f9f9f9;
        }

        button {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        h1 {
            text-align: center;
        }

        .form-row {
            margin-bottom: 20px;
        }

        .form-row label {
            display: block;
            margin-bottom: 10px;
        }

        .form-row div {
            margin-bottom: 10px;
        }

        .StripeElement {
            width: 100%;
        }
    </style>
</head>
<body>
    <h1>Stripe Payment Gateway Test</h1>
    <form id="payment-form">
        <div class="form-row">
            <label for="card-element">
                Credit or debit card
            </label>
            <div id="card-element">
                <!-- A Stripe Element will be inserted here. -->
            </div>
            <!-- Used to display form errors. -->
            <div id="card-errors" role="alert"></div>
        </div>
        <button id="submit">Submit Payment</button>
    </form>

    <script>
        var stripe = Stripe('<?php echo $config['stripe_api_key']; ?>');
        var elements = stripe.elements();

        var style = {
            base: {
                color: '#32325d',
                fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                fontSmoothing: 'antialiased',
                fontSize: '16px',
                '::placeholder': {
                    color: '#aab7c4'
                }
            },
            invalid: {
                color: '#fa755a',
                iconColor: '#fa755a'
            }
        };

        var card = elements.create('card', {style: style});
        card.mount('#card-element');

        card.on('change', function(event) {
            var displayError = document.getElementById('card-errors');
            if (event.error) {
                displayError.textContent = event.error.message;
            } else {
                displayError.textContent = '';
            }
        });

        var form = document.getElementById('payment-form');
        form.addEventListener('submit', function(event) {
            event.preventDefault();

            stripe.createPaymentMethod({
                type: 'card',
                card: card,
            }).then(function(result) {
                if (result.error) {
                    var errorElement = document.getElementById('card-errors');
                    errorElement.textContent = result.error.message;
                } else {
                    handlePayment(result.paymentMethod.id);
                }
            });
        });

        function handlePayment(paymentMethodId) {
            fetch('test_payment.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({payment_method_id: paymentMethodId})
            }).then(function(response) {
                return response.json();
            }).then(function(result) {
                if (result.error) {
                    var errorElement = document.getElementById('card-errors');
                    errorElement.textContent = result.error.message;
                } else {
                    var paymentIntent = result.paymentIntent;
                    alert('Payment successful! PaymentIntent status: ' + paymentIntent.status);
                }
            }).catch(function(error) {
                console.error('Error:', error);
            });
        }
    </script>
</body>
</html>
