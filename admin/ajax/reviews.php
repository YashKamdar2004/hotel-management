<?php 

    require('../inc/db_config.php');
    require('../inc/essentials.php');
    adminLogin(); 

    if(isset($_POST['get_reviews']))
    {   
        $query = "SELECT r.*, u.name as user_name, rm.name as room_name 
                  FROM reviews r 
                  INNER JOIN user_cred u ON r.user_id = u.id 
                  INNER JOIN rooms rm ON r.room_id = rm.id 
                  ORDER BY r.id DESC";
        
        $res = mysqli_query($con, $query);
        $i = 1;
        $data = "";

        while($row = mysqli_fetch_assoc($res))
        {
            $date = date("d-m-Y", strtotime($row['created_at']));

            // Rating stars
            $stars = "";
            for($j = 1; $j <= 5; $j++){
                if($j <= $row['rating']){
                    $stars .= "<i class='bi bi-star-fill text-warning'></i>";
                } else {
                    $stars .= "<i class='bi bi-star text-warning'></i>";
                }
            }

            // Status Badge
            if($row['review_status'] == 'pending'){
                $status = "<span class='badge bg-warning'>Pending</span>";
            } else if($row['review_status'] == 'approved'){
                $status = "<span class='badge bg-success'>Approved</span>";
            } else {
                $status = "<span class='badge bg-danger'>Rejected</span>";
            }

            // Action Buttons
            $actions = "";
            if($row['review_status'] == 'pending'){
                $actions .= "<button onclick='approve_review($row[id])' class='btn btn-success btn-sm shadow-none mb-1'>
                    <i class='bi bi-check-circle'></i> Approve
                </button> ";
                $actions .= "<button onclick='reject_review($row[id])' class='btn btn-danger btn-sm shadow-none mb-1'>
                    <i class='bi bi-x-circle'></i> Reject
                </button> ";
            }
            $actions .= "<button onclick='delete_review($row[id])' class='btn btn-dark btn-sm shadow-none mb-1'>
                <i class='bi bi-trash'></i> Delete
            </button>";

            $review_text = strlen($row['review']) > 100 ? substr($row['review'], 0, 100) . '...' : $row['review'];

            $data .= "
                <tr>
                    <td>$i</td>
                    <td>$row[user_name]</td>
                    <td>$row[room_name]</td>
                    <td>$stars</td>
                    <td class='text-start'>$review_text</td>
                    <td>$status</td>
                    <td>$date</td>
                    <td>$actions</td>
                </tr>  
            ";
            $i++;
        }

        if(empty($data)){
            $data = "<tr><td colspan='8' class='text-center'>No reviews found!</td></tr>";
        }

        echo $data; 
    }

    if(isset($_POST['approve_review']))
    {
        $frm_data = filteration($_POST);
        $q = "UPDATE `reviews` SET `review_status` = 'approved' WHERE `id` = ?";
        $v = [$frm_data['approve_review']];

        if(update($q, $v, 'i')){
            echo 1;
        } else {
            echo 0;
        }
    }

    if(isset($_POST['reject_review']))
    {
        $frm_data = filteration($_POST);
        $q = "UPDATE `reviews` SET `review_status` = 'rejected' WHERE `id` = ?";
        $v = [$frm_data['reject_review']];

        if(update($q, $v, 'i')){
            echo 1;
        } else {
            echo 0;
        }
    }

    if(isset($_POST['delete_review']))
    {
        $frm_data = filteration($_POST);
        $q = "DELETE FROM `reviews` WHERE `id` = ?";
        $v = [$frm_data['delete_review']];

        if(delete($q, $v, 'i')){
            echo 1;
        } else {
            echo 0;
        }
    }
?>
