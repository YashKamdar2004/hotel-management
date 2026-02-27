<?php 

require('../admin/inc/db_config.php');
require('../admin/inc/essentials.php');

session_start();

if(!(isset($_SESSION['login']) && $_SESSION['login'] == true)){
    exit;
}

if(isset($_POST['get_bookings']))
{   
    $query = "SELECT b.*, r.name as room_name 
              FROM bookings b 
              INNER JOIN rooms r ON b.room_id = r.id 
              WHERE b.user_id = ? 
              ORDER BY b.id DESC";
    
    $res = select($query, [$_SESSION['uId']], 'i');
    $i = 1;
    $data = "";

    while($row = mysqli_fetch_assoc($res))
    {
        $checkin = date("d-m-Y", strtotime($row['checkin']));
        $checkout = date("d-m-Y", strtotime($row['checkout']));
        $date = date("d-m-Y", strtotime($row['created_at']));

        // Booking Status Badge
        if($row['booking_status'] == 'pending'){
            $booking_status = "<span class='badge bg-warning'>Pending</span>";
        } else if($row['booking_status'] == 'confirmed'){
            $booking_status = "<span class='badge bg-success'>Confirmed</span>";
        } else if($row['booking_status'] == 'completed'){
            $booking_status = "<span class='badge bg-primary'>Completed</span>";
        } else if($row['booking_status'] == 'refund_requested'){
            $booking_status = "<span class='badge bg-info'>Refund Requested</span>";
        } else {
            $booking_status = "<span class='badge bg-danger'>Cancelled</span>";
        }

        // Payment Status Badge
        if($row['payment_status'] == 'paid'){
            $payment_status = "<span class='badge bg-success'>Paid</span>";
        } else if($row['payment_status'] == 'refunded'){
            $payment_status = "<span class='badge bg-secondary'>Refunded</span>";
        } else if($row['payment_status'] == 'failed'){
            $payment_status = "<span class='badge bg-danger'>Failed</span>";
        } else {
            $payment_status = "<span class='badge bg-warning'>Pending</span>";
        }

        // Action Button Logic
        $action = "";

        if(($row['booking_status'] == 'pending' || $row['booking_status'] == 'confirmed') && $row['payment_status'] == 'paid'){
            $action = "<button onclick='request_refund($row[id])' class='btn btn-warning btn-sm shadow-none mb-1'>
                <i class='bi bi-arrow-counterclockwise'></i> Request Refund
            </button>";
        } else if($row['booking_status'] == 'refund_requested'){
            $action = "<span class='badge bg-info'>Refund Requested</span>";
        } else if($row['booking_status'] == 'completed'){
            // Check if review already submitted
            $review_check = select("SELECT * FROM `reviews` WHERE `booking_id` = ?", [$row['id']], 'i');
            if(mysqli_num_rows($review_check) == 0){
                $action = "<button onclick='open_review_modal($row[id], $row[room_id])' class='btn btn-success btn-sm shadow-none mb-1'>
                    <i class='bi bi-star-fill'></i> Write Review
                </button>";
            } else {
                $action = "<span class='badge bg-success'>Reviewed</span>";
            }
        } else {
            $action = "<span class='text-muted'>No Action</span>";
        }
        
        // PDF download button in separate column
        $pdf_btn = "<a href='generate_booking_pdf.php?id=$row[id]' class='btn btn-primary btn-sm shadow-none' target='_blank'>
            <i class='bi bi-file-pdf'></i> Download
        </a>";

        $data .= "
            <tr>
                <td>$i</td>
                <td>$row[room_name]</td>
                <td>$checkin</td>
                <td>$checkout</td>
                <td>₹$row[total_amount]</td>
                <td>$booking_status</td>
                <td>$payment_status</td>
                <td>$date</td>
                <td>$action</td>
                <td>$pdf_btn</td>
            </tr>  
        ";
        $i++;
    }

    if(empty($data)){
        $data = "<tr><td colspan='10' class='text-center'>No bookings found!</td></tr>";
    }

    echo $data; 
}

if(isset($_POST['request_refund']))
{
    $frm_data = filteration($_POST);
    $booking_id = $frm_data['request_refund'];

    // Log for debugging
    error_log("Refund request received for booking ID: " . $booking_id);

    // Verify booking belongs to logged-in user
    $verify_query = "SELECT * FROM `bookings` WHERE `id` = ? AND `user_id` = ?";
    $verify_res = select($verify_query, [$booking_id, $_SESSION['uId']], 'ii');

    if(mysqli_num_rows($verify_res) == 0){
        error_log("Booking not found or doesn't belong to user");
        echo 'not_found';
        exit;
    }

    $booking = mysqli_fetch_assoc($verify_res);
    error_log("Current booking status: " . $booking['booking_status'] . ", payment: " . $booking['payment_status']);

    // Check if already refund_requested
    if($booking['booking_status'] == 'refund_requested'){
        error_log("Already requested");
        echo 'already_requested';
        exit;
    }

    // Check if eligible for refund
    if($booking['payment_status'] != 'paid'){
        error_log("Payment not paid");
        echo 'payment_not_paid';
        exit;
    }

    if($booking['booking_status'] != 'confirmed' && $booking['booking_status'] != 'pending'){
        error_log("Invalid status: " . $booking['booking_status']);
        echo 'invalid_status';
        exit;
    }

    // Update booking status to refund_requested - USE DIRECT QUERY
    $con = $GLOBALS['con'];
    $update_query = "UPDATE `bookings` SET `booking_status` = 'refund_requested' WHERE `id` = $booking_id";
    $update_result = mysqli_query($con, $update_query);
    
    if($update_result){
        // Verify the update
        $check_query = "SELECT booking_status FROM `bookings` WHERE `id` = $booking_id";
        $check_res = mysqli_query($con, $check_query);
        $check_data = mysqli_fetch_assoc($check_res);
        error_log("After update, booking status is: " . $check_data['booking_status']);
        
        echo 1;
    } else {
        error_log("Database update failed: " . mysqli_error($con));
        echo 0;
    }
}
?>
