<?php
// ------------------------------------------------------------
// payment.php – Hotel Booking System Payment Page
// ------------------------------------------------------------

// ------------------------------------------------------------------
// 1️⃣ Retrieve the amount (and other booking details) from the URL.
//    These values are also used later when inserting the booking.
// ------------------------------------------------------------------
$amount   = isset($_GET['amount'])   ? $_GET['amount']   : 0;
$room_id  = isset($_GET['room_id'])  ? $_GET['room_id']  : null;
$checkin  = isset($_GET['checkin'])  ? $_GET['checkin']  : null;
$checkout = isset($_GET['checkout']) ? $_GET['checkout'] : null;

// ------------------------------------------------------------------
// 2️⃣ AJAX POST – process the payment and create the booking record.
// ------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'process') {
    header('Content-Type: application/json');

    // --------------------------------------------------------------
    // 2.1 Validate payment method (sent via POST)
    // --------------------------------------------------------------
    if (empty($_POST['payMethod'])) {
        echo json_encode(['success' => false, 'message' => 'Missing payment method']);
        exit;
    }

    // --------------------------------------------------------------
    // 2.2 Validate required GET parameters (booking data)
    // --------------------------------------------------------------
    if (empty($room_id) || empty($checkin) || empty($checkout)) {
        echo json_encode(['success' => false, 'message' => 'Missing booking data']);
        exit;
    }

    // --------------------------------------------------------------
    // 2.3 Basic amount & date validation
    // --------------------------------------------------------------
    $amount = floatval($amount);
    if ($amount <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid amount']);
        exit;
    }
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $checkin) ||
        !preg_match('/^\d{4}-\d{2}-\d{2}$/', $checkout)) {
        echo json_encode(['success' => false, 'message' => 'Invalid date format']);
        exit;
    }

    // --------------------------------------------------------------
    // 2.4 DB connection (uses the correct database – hbwebsite)
    // --------------------------------------------------------------
    require_once __DIR__ . '/db_connect.php';

    // --------------------------------------------------------------
    // 2.5 Generate unique IDs for this booking
    // --------------------------------------------------------------
    $booking_id = 'HB' . rand(10000, 99999);
    $txn_id    = 'TXN' . rand(100000, 999999);

    // --------------------------------------------------------------
    // 2.6 Insert the booking record (method column removed)
    // --------------------------------------------------------------
    try {
        $stmt = $pdo->prepare(
            "INSERT INTO bookings (booking_id, txn_id, room_id, checkin, checkout, amount)
             VALUES (?,?,?,?,?,?)"
        );
        $stmt->execute([$booking_id, $txn_id, $room_id, $checkin, $checkout, $amount]);

        // ----------------------------------------------------------
        // 2.7 Return success JSON
        // ----------------------------------------------------------
        echo json_encode([
            'success'    => true,
            'booking_id' => $booking_id,
            'txn_id'     => $txn_id,
            'message'    => 'Payment successful'
        ]);
    } catch (PDOException $e) {
        error_log('Booking insertion failed: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
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
        .payment-card { max-width:500px; margin:2rem auto; box-shadow:0 .5rem 1rem rgba(0,0,0,.15); border-radius:.75rem; }
        .spinner-border { width:2rem; height:2rem; }
    </style>
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="payment-card bg-white p-4">
        <h4 class="mb-4 text-center">Total Amount: ₹<?php echo htmlspecialchars($amount); ?></h4>

        <form id="paymentForm" novalidate>
            <!-- Payment method radios -->
            <div class="mb-3">
                <label class="form-label fw-bold">Select Payment Method</label>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="payMethod"
                           id="upiOption" value="upi" checked>
                    <label class="form-check-label" for="upiOption">UPI</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="payMethod"
                           id="cardOption" value="card">
                    <label class="form-check-label" for="cardOption">Credit/Debit Card</label>
                </div>
            </div>

            <!-- UPI fields -->
            <div id="upiFields" class="mb-3">
                <label for="upiId" class="form-label">UPI ID</label>
                <input type="text" class="form-control" id="upiId" name="upiId"
                       placeholder="example@upi" required>
                <div class="invalid-feedback">Please enter a valid UPI ID.</div>
            </div>

            <!-- Card fields (hidden by default) -->
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

            <!-- Submit button + spinner -->
            <div class="d-grid gap-2 mt-4">
                <button type="submit" class="btn btn-primary" id="payBtn">Pay Now</button>
                <div id="spinner" class="text-center d-none">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
            </div>

            <div id="alertPlaceholder" class="mt-3"></div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// ------------------------------------------------------------
// Toggle fields based on selected payment method
// ------------------------------------------------------------
document.querySelectorAll('input[name="payMethod"]').forEach(radio => {
    radio.addEventListener('change', togglePaymentFields);
});
function togglePaymentFields() {
    const isUPI = document.getElementById('upiOption').checked;
    document.getElementById('upiFields').classList.toggle('d-none', !isUPI);
    document.getElementById('cardFields').classList.toggle('d-none', isUPI);
}

// ------------------------------------------------------------
// Validation helpers
// ------------------------------------------------------------
function setInvalid(elem, message) {
    elem.classList.add('is-invalid');
    elem.nextElementSibling.textContent = message;
}
function clearInvalid(elem) {
    elem.classList.remove('is-invalid');
}

// ------------------------------------------------------------
// AJAX payment simulation
// ------------------------------------------------------------
document.getElementById('paymentForm').addEventListener('submit', function (e) {
    e.preventDefault();
    document.getElementById('alertPlaceholder').innerHTML = '';

    // ----- client‑side validation -----
    let valid = true;
    const payMethod = document.querySelector('input[name="payMethod"]:checked').value;

    if (payMethod === 'upi') {
        const upiId = document.getElementById('upiId');
        if (!upiId.value.trim()) { setInvalid(upiId, 'Please enter a valid UPI ID.'); valid = false; }
        else { clearInvalid(upiId); }
    } else {
        const cardNumber = document.getElementById('cardNumber');
        const cleanNumber = cardNumber.value.replace(/\s+/g, '');
        if (!/^\d{16}$/.test(cleanNumber)) { setInvalid(cardNumber, 'Enter a 16‑digit card number.'); valid = false; }
        else { clearInvalid(cardNumber); }

        const cardHolder = document.getElementById('cardHolder');
        if (!cardHolder.value.trim()) { setInvalid(cardHolder, 'Enter the name on the card.'); valid = false; }
        else { clearInvalid(cardHolder); }

        const expiryDate = document.getElementById('expiryDate');
        if (!/^(0[1-9]|1[0-2])\/\d{2}$/.test(expiryDate.value.trim())) { setInvalid(expiryDate, 'Enter expiry in MM/YY format.'); valid = false; }
        else { clearInvalid(expiryDate); }

        const cvv = document.getElementById('cvv');
        if (!/^\d{3}$/.test(cvv.value.trim())) { setInvalid(cvv, 'Enter a 3‑digit CVV.'); valid = false; }
        else { clearInvalid(cvv); }
    }

    if (!valid) return;

    // ----- show spinner -----
    document.getElementById('payBtn').disabled = true;
    document.getElementById('spinner').classList.remove('d-none');

    // ----- send AJAX request (keeps the query string) -----
    const formData = new FormData(this);
    formData.append('action', 'process');

    fetch(window.location.href, {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        setTimeout(() => {
            document.getElementById('spinner').classList.add('d-none');
            document.getElementById('payBtn').disabled = false;

            const alertDiv = document.createElement('div');
            if (data.success) {
                alertDiv.className = 'alert alert-success';
                alertDiv.textContent = data.message;

                // Redirect to booking_success.php with the generated IDs
                const redirectUrl = `booking_success.php?amount=${encodeURIComponent(<?php echo $amount; ?>)}&room_id=${encodeURIComponent(<?php echo $room_id; ?>)}&checkin=${encodeURIComponent(<?php echo $checkin; ?>)}&checkout=${encodeURIComponent(<?php echo $checkout; ?>)}&booking_id=${encodeURIComponent(data.booking_id)}&txn_id=${encodeURIComponent(data.txn_id)}`;
                setTimeout(() => { window.location.href = redirectUrl; }, 1500);
            } else {
                alertDiv.className = 'alert alert-danger';
                alertDiv.textContent = data.message;
            }
            document.getElementById('alertPlaceholder').appendChild(alertDiv);
        }, 2000);
    })
    .catch(() => {
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