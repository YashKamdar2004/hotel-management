<?php

require('../admin/inc/db_config.php');
require('../admin/inc/essentials.php');

session_start();

if(!(isset($_SESSION['login']) && $_SESSION['login'] == true)){
    echo 'not_logged_in';
    exit;
}

if(isset($_POST['submit_review']))
{
    $data = filteration($_POST);
    $user_id = $_SESSION['uId'];
    $booking_id = $data['booking_id'];
    $room_id = $data['room_id'];
    $rating = $data['rating'];
    $review = $data['review'];

    // Verify booking belongs to user and is completed
    $verify_query = "SELECT * FROM `bookings` WHERE `id` = ? AND `user_id` = ? AND `booking_status` = 'completed'";
    $verify_res = select($verify_query, [$booking_id, $user_id], 'ii');

    if(mysqli_num_rows($verify_res) == 0){
        echo 0;
        exit;
    }

    // Check if review already exists
    $review_check = select("SELECT * FROM `reviews` WHERE `booking_id` = ?", [$booking_id], 'i');
    if(mysqli_num_rows($review_check) > 0){
        echo 'already_reviewed';
        exit;
    }

    // Insert review
    $query = "INSERT INTO `reviews` (`booking_id`, `user_id`, `room_id`, `rating`, `review`, `review_status`) VALUES (?, ?, ?, ?, ?, 'pending')";
    $values = [$booking_id, $user_id, $room_id, $rating, $review];

    if(insert($query, $values, 'iiiis')){
        echo 1;
    } else {
        echo 0;
    }
}

?>
