<?php
require('admin/inc/db_config.php');
require('admin/inc/essentials.php');

session_start();

// Check if user is logged in
if(!(isset($_SESSION['login']) && $_SESSION['login'] == true)){
    redirect('rooms.php');
}

// Validate session data
if(!isset($_SESSION['room']) || !$_SESSION['room']['available']){
    redirect('rooms.php');
}

// Get POST data
$name = $_POST['name'];
$phonenum = $_POST['phonenum'];
$address = $_POST['address'];
$checkin = $_POST['checkin'];
$checkout = $_POST['checkout'];

$user_id = $_SESSION['uId'];
$room_id = $_SESSION['room']['id'];
$total_amount = $_SESSION['room']['payment'];

// Insert booking with pending status
$query = "INSERT INTO `bookings` (`user_id`, `room_id`, `name`, `phonenum`, `address`, `checkin`, `checkout`, `total_amount`, `booking_status`, `payment_status`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending', 'unpaid')";

$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "iisssssd", $user_id, $room_id, $name, $phonenum, $address, $checkin, $checkout, $total_amount);

if(mysqli_stmt_execute($stmt)){
    $booking_id = mysqli_insert_id($con);
    
    // Store booking ID in session
    $_SESSION['booking_id'] = $booking_id;
    
    // Redirect to payment page
    redirect("payment.php?booking_id=$booking_id");
} else {
    echo "Booking failed. Please try again.";
}

mysqli_stmt_close($stmt);
?>
