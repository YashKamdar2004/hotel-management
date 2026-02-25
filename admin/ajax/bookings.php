<?php 

    require('../inc/db_config.php');
    require('../inc/essentials.php');
    adminLogin(); 

    if(isset($_POST['get_bookings']))
    {   
        $query = "SELECT b.*, u.name as user_name, r.name as room_name 
                  FROM bookings b 
                  INNER JOIN user_cred u ON b.user_id = u.id 
                  INNER JOIN rooms r ON b.room_id = r.id 
                  ORDER BY b.id DESC";
        
        $res = mysqli_query($con, $query);
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
                $booking_status = "<span class='badge bg-info'>Completed</span>";
            } else if($row['booking_status'] == 'refund_requested'){
                $booking_status = "<span class='badge bg-warning'>Refund Requested</span>";
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
                $payment_status = "<span class='badge bg-warning'>Unpaid</span>";
            }

            // Action Buttons Logic
            $actions = "";

            if($row['booking_status'] == 'pending' && $row['payment_status'] == 'paid'){
                $actions .= "<button onclick='confirm_booking($row[id])' class='btn btn-success btn-sm shadow-none mb-1'>
                    <i class='bi bi-check-circle'></i> Approve
                </button> ";
                $actions .= "<button onclick='cancel_booking($row[id])' class='btn btn-danger btn-sm shadow-none mb-1'>
                    <i class='bi bi-x-circle'></i> Reject
                </button>";
            }

            if($row['booking_status'] == 'confirmed'){
                $actions .= "<button onclick='complete_booking($row[id])' class='btn btn-info btn-sm shadow-none mb-1'>
                    <i class='bi bi-check-all'></i> Complete
                </button>";
            }

            if($row['booking_status'] == 'refund_requested'){
                $actions .= "<button onclick='accept_refund($row[id])' class='btn btn-success btn-sm shadow-none mb-1'>
                    <i class='bi bi-check-circle'></i> Accept Refund
                </button> ";
                $actions .= "<button onclick='reject_refund($row[id])' class='btn btn-danger btn-sm shadow-none mb-1'>
                    <i class='bi bi-x-circle'></i> Reject Refund
                </button>";
            }

            if(empty($actions)){
                $actions = "<span class='text-muted'>No Action</span>";
            }

            $data .= "
                <tr>
                    <td>$i</td>
                    <td>$row[user_name]</td>
                    <td>$row[room_name]</td>
                    <td>$checkin</td>
                    <td>$checkout</td>
                    <td>₹$row[total_amount]</td>
                    <td>$booking_status</td>
                    <td>$payment_status</td>
                    <td>$date</td>
                    <td>$actions</td>
                </tr>  
            ";
            $i++;
        }

        echo $data; 
    }

    if(isset($_POST['confirm_booking']))
    {
        $frm_data = filteration($_POST);

        $q = "UPDATE `bookings` SET `booking_status`='confirmed' WHERE `id`=?";
        $v = [$frm_data['confirm_booking']];

        if(update($q, $v, 'i')){
            echo 1;
        } else {
            echo 0;
        }
    }

    if(isset($_POST['cancel_booking']))
    {
        $frm_data = filteration($_POST);

        $q = "UPDATE `bookings` SET `booking_status`='cancelled' WHERE `id`=?";
        $v = [$frm_data['cancel_booking']];

        if(update($q, $v, 'i')){
            echo 1;
        } else {
            echo 0;
        }
    }

    if(isset($_POST['complete_booking']))
    {
        $frm_data = filteration($_POST);

        $q = "UPDATE `bookings` SET `booking_status`='completed' WHERE `id`=?";
        $v = [$frm_data['complete_booking']];

        if(update($q, $v, 'i')){
            echo 1;
        } else {
            echo 0;
        }
    }

    if(isset($_POST['search_booking']))
    {   
        $frm_data = filteration($_POST);

        $query = "SELECT b.*, u.name as user_name, r.name as room_name 
                  FROM bookings b 
                  INNER JOIN user_cred u ON b.user_id = u.id 
                  INNER JOIN rooms r ON b.room_id = r.id 
                  WHERE u.name LIKE ? 
                  ORDER BY b.id DESC";

        $res = select($query, ["%$frm_data[name]%"], 's');
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
                $booking_status = "<span class='badge bg-info'>Completed</span>";
            } else if($row['booking_status'] == 'refund_requested'){
                $booking_status = "<span class='badge bg-warning'>Refund Requested</span>";
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
                $payment_status = "<span class='badge bg-warning'>Unpaid</span>";
            }

            // Action Buttons Logic
            $actions = "";

            if($row['booking_status'] == 'pending' && $row['payment_status'] == 'paid'){
                $actions .= "<button onclick='confirm_booking($row[id])' class='btn btn-success btn-sm shadow-none mb-1'>
                    <i class='bi bi-check-circle'></i> Approve
                </button> ";
                $actions .= "<button onclick='cancel_booking($row[id])' class='btn btn-danger btn-sm shadow-none mb-1'>
                    <i class='bi bi-x-circle'></i> Reject
                </button>";
            }

            if($row['booking_status'] == 'confirmed'){
                $actions .= "<button onclick='complete_booking($row[id])' class='btn btn-info btn-sm shadow-none mb-1'>
                    <i class='bi bi-check-all'></i> Complete
                </button>";
            }

            if($row['booking_status'] == 'refund_requested'){
                $actions .= "<button onclick='accept_refund($row[id])' class='btn btn-success btn-sm shadow-none mb-1'>
                    <i class='bi bi-check-circle'></i> Accept Refund
                </button> ";
                $actions .= "<button onclick='reject_refund($row[id])' class='btn btn-danger btn-sm shadow-none mb-1'>
                    <i class='bi bi-x-circle'></i> Reject Refund
                </button>";
            }

            if(empty($actions)){
                $actions = "<span class='text-muted'>No Action</span>";
            }

            $data .= "
                <tr>
                    <td>$i</td>
                    <td>$row[user_name]</td>
                    <td>$row[room_name]</td>
                    <td>$checkin</td>
                    <td>$checkout</td>
                    <td>₹$row[total_amount]</td>
                    <td>$booking_status</td>
                    <td>$payment_status</td>
                    <td>$date</td>
                    <td>$actions</td>
                </tr>  
            ";
            $i++;
        }

        echo $data; 
    }
?>
