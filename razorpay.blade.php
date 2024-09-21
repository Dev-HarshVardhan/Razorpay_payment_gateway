<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Razorpay Payment Integration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Razorpay Payment Form</h2>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div id="error-message" class="alert alert-danger" style="display: none;"></div>
                <form id="razorpay-form" action="" method="POST">
                    @csrf
                    <!-- Laravel CSRF Token -->
                    <!-- <input type="hidden" name="_token" value="{{ csrf_token() }}"> -->
                    
                    <!-- Payment ID (to be filled after Razorpay returns payment ID) -->
                    <input type="hidden" id="razorpay_payment_id" name="razorpay_payment_id">

                    <!-- Name Field -->
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>

                    <!-- Email Field -->
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>

                    <!-- Phone Number Field -->
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="tel" name="phone" class="form-control" required>
                    </div>

                    <!-- Amount Field -->
                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount</label>
                        <input type="number" name="amount" class="form-control" required>
                    </div>

                    <!-- Terms and Conditions -->
                    <div class="mb-3 form-check">
                        <input type="checkbox" name="terms" class="form-check-input" id="terms" required>
                        <label class="form-check-label" for="terms">I agree to the terms and conditions</label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" id="pay-button" class="btn btn-primary w-100">Pay with Razorpay</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('pay-button').onclick = function(e) {
            e.preventDefault(); // Prevent form from submitting

            // Clear any previous error messages
            var errorMessage = document.getElementById('error-message');
            errorMessage.style.display = 'none';
            errorMessage.innerText = '';

            // Client-side validation for required fields
            let name = document.querySelector('input[name="name"]').value;
            let phone = document.querySelector('input[name="phone"]').value;
            let email = document.querySelector('input[name="email"]').value;
            let termsChecked = document.querySelector('input[name="terms"]').checked;

            // Validate if all fields are filled and terms checked
            if (name === '' || phone === '' || email === '' || !termsChecked) {
                errorMessage.innerText = 'Please fill all the required fields and agree to the terms.';
                errorMessage.style.display = 'block';
                return;
            }
            
            // Validate email format
            let emailPattern = /^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/;
            if (!emailPattern.test(email)) {
                errorMessage.innerText = 'Please enter a valid email address.';
                errorMessage.style.display = 'block';
                return;
            }

            // Validate phone number format (simple validation for 10 digits)
            let phonePattern = /^[0-9]{10}$/;
            if (!phonePattern.test(phone)) {
                errorMessage.innerText = 'Please enter a valid 10-digit phone number.';
                errorMessage.style.display = 'block';
                return;
            }

            // Get the amount (converting to paise)
            var amount = document.querySelector('input[name="amount"]').value * 100; // Razorpay expects amount in paise

            var options = {
                "key": "{{ env('RAZORPAY_KEY')  }}", // Your Razorpay Key ID from the .env file
                "amount": amount,
                "currency": "INR",
                "name": "Tripmoments",
                "description": "Test transaction",
                "image": "https://tripmoments.in/assets/front/img/logo.png",
                "prefill": {
                    "name": name,
                    "email": email
                },
                "theme": {
                    "color": "#0d6efd"
                },
                "handler": function(response) {
                    document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;
                    document.getElementById('razorpay-form').submit(); // Submit form after successful payment
                },
                "modal": {
                    "ondismiss": function() {
                        alert("Payment was dismissed");
                    }
                }
            };

            var rzp = new Razorpay(options);
            rzp.open();
        };
    </script>
</body>
</html>
