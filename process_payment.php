<?php
require('admin/inc/db_config.php');
require('admin/inc/essentials.php');

session_start();

header('Content-Type: application/json');

// Check if user is logged in
if(!isset($_SESSION['login']) || $_SESSION['login'] != true){
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit;
}

if(!isset($_POST['booking_id']) || !isset($_POST['payment_method'])){
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    exit;
}

$booking_id = filteration($_POST)['booking_id'];
$payment_method = filteration($_POST)['payment_method'];

// Verify booking exists and belongs to user
$booking_res = select("SELECT * FROM `bookings` WHERE `id` = ? AND `user_id` = ?", 
                      [$booking_id, $_SESSION['uId']], "ii");

if(mysqli_num_rows($booking_res) == 0){
    echo json_encode(['status' => 'error', 'message' => 'Booking not found']);
    exit;
}

$booking_data = mysqli_fetch_assoc($booking_res);

// Generate random transaction ID
$transaction_id = 'TXN' . strtoupper(uniqid()) . rand(1000, 9999);

// Simulate payment: 80% success, 20% failure
$random = rand(1, 100);
$payment_success = ($random <= 80);

if($payment_success) {
    // Payment Success - Keep booking pending for admin approval
    
    // Update payment status only
    $update_booking = "UPDATE `bookings` SET `payment_status` = 'paid' WHERE `id` = ?";
    $stmt = mysqli_prepare($con, $update_booking);
    mysqli_stmt_bind_param($stmt, "i", $booking_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    
    // Insert payment record
    $insert_payment = "INSERT INTO `payments` (`booking_id`, `transaction_id`, `payment_method`, `amount`, `status`) VALUES (?, ?, ?, ?, 'success')";
    $stmt = mysqli_prepare($con, $insert_payment);
    mysqli_stmt_bind_param($stmt, "issd", $booking_id, $transaction_id, $payment_method, $booking_data['total_amount']);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    
    echo json_encode([
        'status' => 'success',
        'transaction_id' => $transaction_id,
        'booking_id' => $booking_id
    ]);
    
} else {
    // Payment Failed
    
    // Update booking status
    $update_booking = "UPDATE `bookings` SET `booking_status` = 'cancelled', `payment_status` = 'failed' WHERE `id` = ?";
    $stmt = mysqli_prepare($con, $update_booking);
    mysqli_stmt_bind_param($stmt, "i", $booking_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    
    // Insert payment record
    $insert_payment = "INSERT INTO `payments` (`booking_id`, `transaction_id`, `payment_method`, `amount`, `status`) VALUES (?, ?, ?, ?, 'failed')";
    $stmt = mysqli_prepare($con, $insert_payment);
    mysqli_stmt_bind_param($stmt, "issd", $booking_id, $transaction_id, $payment_method, $booking_data['total_amount']);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    
    echo json_encode([
        'status' => 'failed',
        'transaction_id' => $transaction_id,
        'booking_id' => $booking_id
    ]);
}
?>
