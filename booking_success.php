<?php
session_start();

// If user directly tries to access page
if (!isset($_GET['amount']) || !isset($_GET['room_id'])
|| !isset($_GET['checkin']) || !isset($_GET['checkout'])) {
    header("Location: index.php");
    exit;
}

$amount = $_GET['amount'];
$room_id = $_GET['room_id'];
$checkin = $_GET['checkin'];
$checkout = $_GET['checkout'];
// Use IDs passed from payment page if available, otherwise generate new ones
if (isset($_GET['booking_id']) && isset($_GET['txn_id'])) {
    $booking_id = $_GET['booking_id'];
    $txn_id = $_GET['txn_id'];
}
else {
    $booking_id = "HB" . rand(10000, 99999);
    $txn_id = "TXN" . rand(100000, 999999);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Booking Confirmed</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body{
            background: linear-gradient(135deg, #28a745, #218838);
        }
        .success-card{
            max-width:600px;
            margin:80px auto;
            background:white;
            padding:30px;
            border-radius:15px;
            box-shadow:0 15px 35px rgba(0,0,0,0.2);
        }
    </style>
</head>

<body>

<div class="success-card text-center">

    <h2 class="text-success mb-3">🎉 Booking Confirmed!</h2>

    <p class="lead">Your payment was successful.</p>

    <hr>

    <div class="text-start mt-4">

        <p><strong>Booking ID:</strong> <?php echo $booking_id; ?></p>
        <p><strong>Transaction ID:</strong> <?php echo $txn_id; ?></p>
        <p><strong>Room ID:</strong> <?php echo $room_id; ?></p>
        <p><strong>Check-in Date:</strong> <?php echo $checkin; ?></p>
        <p><strong>Check-out Date:</strong> <?php echo $checkout; ?></p>
        <p><strong>Amount Paid:</strong> ₹<?php echo $amount; ?></p>

    </div>

    <div class="mt-4">
        <button onclick="window.print()" class="btn btn-outline-primary me-2">
            Print Invoice
        </button>

        <a href="index.php" class="btn btn-success">
            Go to Home
        </a>
    </div>

</div>

</body>
</html>