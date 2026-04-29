// Razorpay Checkout Integration
// Include this script in your payment page

// Initialize Razorpay
function initializeRazorpay(orderId, amount, currency = 'INR', keyId) {
    const options = {
        key: keyId, // Your Razorpay Key ID
        amount: amount * 100, // Amount in paisa
        currency: currency,
        order_id: orderId,
        name: 'GymHub SaaS',
        description: 'Gym Membership Payment',
        image: '/images/logo.png', // Your logo URL

        handler: function (response) {
            // Handle successful payment
            handlePaymentSuccess(response);
        },

        prefill: {
            name: '{{ auth()->user()->name }}',
            email: '{{ auth()->user()->email }}',
            contact: '{{ auth()->user()->phone ?? "" }}',
        },

        notes: {
            address: 'Gym Membership Payment',
        },

        theme: {
            color: '#0abf8e', // Your brand color
        },

        modal: {
            ondismiss: function() {
                console.log('Payment modal dismissed');
            }
        }
    };

    const rzp = new Razorpay(options);

    // Handle payment failure
    rzp.on('payment.failed', function (response) {
        handlePaymentFailure(response);
    });

    return rzp;
}

// Handle successful payment
function handlePaymentSuccess(response) {
    console.log('Payment successful:', response);

    // Send payment details to your backend for verification
    fetch('/api/payment/verify', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            razorpay_order_id: response.razorpay_order_id,
            razorpay_payment_id: response.razorpay_payment_id,
            razorpay_signature: response.razorpay_signature
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Redirect to success page
            window.location.href = '/payment/success?payment_id=' + data.payment_id;
        } else {
            alert('Payment verification failed: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Verification error:', error);
        alert('Payment verification failed. Please contact support.');
    });
}

// Handle payment failure
function handlePaymentFailure(response) {
    console.log('Payment failed:', response);

    alert('Payment failed: ' + response.error.description);

    // You can send failure details to your backend if needed
    fetch('/api/payment/failed', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            order_id: response.error.metadata.order_id,
            payment_id: response.error.metadata.payment_id,
            error_code: response.error.code,
            error_description: response.error.description
        })
    });
}

// Example usage:
// Call this function when user clicks pay button
function initiatePayment(orderData) {
    const rzp = initializeRazorpay(
        orderData.id,
        orderData.amount,
        orderData.currency,
        orderData.razorpay_key
    );

    rzp.open();
}

// Fetch order from backend and initiate payment
document.getElementById('pay-button').addEventListener('click', function() {
    // Show loading
    this.disabled = true;
    this.textContent = 'Processing...';

    // Create order via API
    fetch('/api/payment/create-order', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            amount: 1000, // Amount in rupees
            currency: 'INR',
            receipt: 'receipt_' + Date.now(),
            member_id: {{ auth()->user()->id }}
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.order) {
            initiatePayment({
                id: data.order.id,
                amount: data.order.amount,
                currency: data.order.currency,
                razorpay_key: data.razorpay_key
            });
        } else {
            alert('Order creation failed: ' + data.error);
            document.getElementById('pay-button').disabled = false;
            document.getElementById('pay-button').textContent = 'Pay Now';
        }
    })
    .catch(error => {
        console.error('Order creation error:', error);
        alert('Failed to create payment order');
        document.getElementById('pay-button').disabled = false;
        document.getElementById('pay-button').textContent = 'Pay Now';
    });
});