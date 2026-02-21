<?php
// ------------------------------------------------------------
// payment.php – Hotel Booking System Payment Page
// ------------------------------------------------------------
// Tech Stack: PHP + JavaScript + AJAX + Bootstrap 5
// ------------------------------------------------------------

// 1️⃣ Retrieve total amount from URL
$amount = isset($_GET['amount']) ? $_GET['amount'] : '0';

// ------------------------------------------------------------
// 2️⃣ If this request is an AJAX POST, simulate payment outcome
// ------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'process') {
    // Simulate processing delay (handled on client side)
    // Randomly decide success (true) or failure (false)
    $isSuccess = (bool)random_int(0, 1);
    header('Content-Type: application/json');
    echo json_encode(['success' => $isSuccess]);
    exit;
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Payment – Hotel Booking</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Centered card with subtle shadow */
        .payment-card {
            max-width: 500px;
            margin: 2rem auto;
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,.15);
            border-radius: .75rem;
        }
        .spinner-border {
            width: 2rem;
            height: 2rem;
        }
    </style>
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="payment-card bg-white p-4">
        <!-- 2️⃣ Display total amount -->
        <h4 class="mb-4 text-center">Total Amount: ₹<?php echo htmlspecialchars($amount); ?></h4>

        <!-- 4️⃣ Payment Options -->
        <form id="paymentForm" novalidate>
            <div class="mb-3">
                <label class="form-label fw-bold">Select Payment Method</label>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="payMethod" id="upiOption" value="upi" checked>
                    <label class="form-check-label" for="upiOption">UPI</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="payMethod" id="cardOption" value="card">
                    <label class="form-check-label" for="cardOption">Credit/Debit Card</label>
                </div>
            </div>

            <!-- 5️⃣ UPI Input -->
            <div id="upiFields" class="mb-3">
                <label for="upiId" class="form-label">UPI ID</label>
                <input type="text" class="form-control" id="upiId" name="upiId" placeholder="example@upi" required>
                <div class="invalid-feedback">Please enter a valid UPI ID.</div>
            </div>

            <!-- 6️⃣ Card Inputs (hidden by default) -->
            <div id="cardFields" class="mb-3 d-none">
                <label for="cardNumber" class="form-label">Card Number</label>
                <input type="text" class="form-control" id="cardNumber" name="cardNumber"
                       placeholder="1234 5678 9012 3456" maxlength="19" required>
                <div class="invalid-feedback">Enter a 16‑digit card number.</div>

                <label for="cardHolder" class="form-label mt-3">Card Holder Name</label>
                <input type="text" class="form-control" id="cardHolder" name="cardHolder" required>
                <div class="invalid-feedback">Enter the name on the card.</div>

                <div class="row mt-3">
                    <div class="col-6">
                        <label for="expiryDate" class="form-label">Expiry Date</label>
                        <input type="text" class="form-control" id="expiryDate" name="expiryDate"
                               placeholder="MM/YY" maxlength="5" required>
                        <div class="invalid-feedback">Enter expiry in MM/YY format.</div>
                    </div>
                    <div class="col-6">
                        <label for="cvv" class="form-label">CVV</label>
                        <input type="password" class="form-control" id="cvv" name="cvv"
                               placeholder="123" maxlength="3" required>
                        <div class="invalid-feedback">Enter a 3‑digit CVV.</div>
                    </div>
                </div>
            </div>

            <!-- 8️⃣ Pay Now button + spinner placeholder -->
            <div class="d-grid gap-2 mt-4">
                <button type="submit" class="btn btn-primary" id="payBtn">Pay Now</button>
                <div id="spinner" class="text-center d-none">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
            </div>

            <!-- 9️⃣/🔟 Alert placeholders -->
            <div id="alertPlaceholder" class="mt-3"></div>
        </form>
    </div>
</div>

<!-- Bootstrap 5 JS + Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
/* ------------------------------------------------------------
   7️⃣ Toggle fields based on selected payment method
   ------------------------------------------------------------ */
document.querySelectorAll('input[name="payMethod"]').forEach(radio => {
    radio.addEventListener('change', togglePaymentFields);
});

function togglePaymentFields() {
    const isUPI = document.getElementById('upiOption').checked;
    document.getElementById('upiFields').classList.toggle('d-none', !isUPI);
    document.getElementById('cardFields').classList.toggle('d-none', isUPI);
}

/* ------------------------------------------------------------
   7️⃣ Client‑side validation helpers
   ------------------------------------------------------------ */
function setInvalid(elem, message) {
    elem.classList.add('is-invalid');
    elem.nextElementSibling.textContent = message;
}
function clearInvalid(elem) {
    elem.classList.remove('is-invalid');
}

/* ------------------------------------------------------------
   8️⃣ AJAX payment simulation
   ------------------------------------------------------------ */
document.getElementById('paymentForm').addEventListener('submit', function (e) {
    e.preventDefault();
    // Reset previous alerts
    document.getElementById('alertPlaceholder').innerHTML = '';

    // 7️⃣ Validate inputs
    let valid = true;
    const payMethod = document.querySelector('input[name="payMethod"]:checked').value;

    if (payMethod === 'upi') {
        const upiId = document.getElementById('upiId');
        if (!upiId.value.trim()) {
            setInvalid(upiId, 'Please enter a valid UPI ID.');
            valid = false;
        } else {
            clearInvalid(upiId);
        }
    } else {
        // Card validation
        const cardNumber = document.getElementById('cardNumber');
        const cleanNumber = cardNumber.value.replace(/\s+/g, '');
        if (!/^\d{16}$/.test(cleanNumber)) {
            setInvalid(cardNumber, 'Enter a 16‑digit card number.');
            valid = false;
        } else {
            clearInvalid(cardNumber);
        }

        const cardHolder = document.getElementById('cardHolder');
        if (!cardHolder.value.trim()) {
            setInvalid(cardHolder, 'Enter the name on the card.');
            valid = false;
        } else {
            clearInvalid(cardHolder);
        }

        const expiryDate = document.getElementById('expiryDate');
        if (!/^(0[1-9]|1[0-2])\/\d{2}$/.test(expiryDate.value.trim())) {
            setInvalid(expiryDate, 'Enter expiry in MM/YY format.');
            valid = false;
        } else {
            clearInvalid(expiryDate);
        }

        const cvv = document.getElementById('cvv');
        if (!/^\d{3}$/.test(cvv.value.trim())) {
            setInvalid(cvv, 'Enter a 3‑digit CVV.');
            valid = false;
        } else {
            clearInvalid(cvv);
        }
    }

    if (!valid) return; // Stop if validation fails

    // Show spinner
    document.getElementById('payBtn').disabled = true;
    document.getElementById('spinner').classList.remove('d-none');

    // Prepare data for AJAX
    const formData = new FormData(this);
    formData.append('action', 'process');

    // 8️⃣ AJAX POST to the same file
    fetch('payment.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        // Simulate 2‑second processing delay
        setTimeout(() => {
            document.getElementById('spinner').classList.add('d-none');
            document.getElementById('payBtn').disabled = false;

            const alertDiv = document.createElement('div');
            if (data.success) {
                alertDiv.className = 'alert alert-success';
                alertDiv.textContent = 'Payment Successful';

                setTimeout(() => {
                    window.location.href = 
                    "booking_success.php?amount=<?php echo urlencode($amount); ?>&room_id=<?php echo urlencode($_GET['room_id']); ?>&checkin=<?php echo urlencode($_GET['checkin']); ?>&checkout=<?php echo urlencode($_GET['checkout']); ?>";
                }, 2000);
            }
            else 
            {
                alertDiv.className = 'alert alert-danger';
                alertDiv.textContent = 'Payment Failed. Please try again.';
            }
            document.getElementById('alertPlaceholder').appendChild(alertDiv);
        }, 2000);
    })
    .catch(() => {
        // Network / unexpected error
        document.getElementById('spinner').classList.add('d-none');
        document.getElementById('payBtn').disabled = false;
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-danger';
        alertDiv.textContent = 'An error occurred. Please try again.';
        document.getElementById('alertPlaceholder').appendChild(alertDiv);
    });
});


</script>
</body>
</html>