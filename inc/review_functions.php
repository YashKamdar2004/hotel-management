<?php

// Function to get approved reviews for a room
function get_room_reviews($room_id) {
    global $con;
    
    $query = "SELECT r.*, u.name as user_name 
              FROM reviews r 
              INNER JOIN user_cred u ON r.user_id = u.id 
              WHERE r.room_id = ? AND r.review_status = 'approved' 
              ORDER BY r.created_at DESC";
    
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "i", $room_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    return $result;
}

// Function to get average rating for a room
function get_room_rating($room_id) {
    global $con;
    
    $query = "SELECT AVG(rating) as avg_rating, COUNT(*) as total_reviews 
              FROM reviews 
              WHERE room_id = ? AND review_status = 'approved'";
    
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "i", $room_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_assoc($result);
    
    return [
        'avg_rating' => $data['avg_rating'] ? round($data['avg_rating'], 1) : 0,
        'total_reviews' => $data['total_reviews']
    ];
}

// Function to display star rating HTML
function display_stars($rating) {
    $stars = "";
    for($i = 1; $i <= 5; $i++){
        if($i <= $rating){
            $stars .= "<i class='bi bi-star-fill text-warning'></i>";
        } else {
            $stars .= "<i class='bi bi-star text-warning'></i>";
        }
    }
    return $stars;
}

?>
