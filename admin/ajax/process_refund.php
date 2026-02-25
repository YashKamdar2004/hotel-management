<?php 

    require('../inc/db_config.php');
    require('../inc/essentials.php');
    adminLogin(); 

    if(isset($_POST['accept_refund']))
    {
        $frm_data = filteration($_POST);
        $booking_id = $frm_data['accept_refund'];

        // Update bookings table
        $q1 = "UPDATE `bookings` SET `booking_status`='cancelled', `payment_status`='refunded' WHERE `id`=?";
        $v1 = [$booking_id];
        
        $update_booking = update($q1, $v1, 'i');

        // Update payments table
        $q2 = "UPDATE `payments` SET `refund_status`='refunded', `refund_date`=NOW() WHERE `booking_id`=?";
        $v2 = [$booking_id];
        
        $update_payment = update($q2, $v2, 'i');

        if($update_booking){
            echo 1;
        } else {
            echo 0;
        }
    }

    if(isset($_POST['reject_refund']))
    {
        $frm_data = filteration($_POST);

        $q = "UPDATE `bookings` SET `booking_status`='confirmed' WHERE `id`=?";
        $v = [$frm_data['reject_refund']];

        if(update($q, $v, 'i')){
            echo 1;
        } else {
            echo 0;
        }
    }
?>
