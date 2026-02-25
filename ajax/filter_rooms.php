<?php

require('../admin/inc/db_config.php');
require('../admin/inc/essentials.php');

// Handle features and facilities arrays separately
$features = isset($_POST['features']) ? $_POST['features'] : [];
$facilities = isset($_POST['facilities']) ? $_POST['facilities'] : [];

// Get availability dates from URL if coming from index.php
session_start();
$checkin_date = isset($_GET['checkin']) ? $_GET['checkin'] : (isset($_POST['checkin']) ? $_POST['checkin'] : null);
$checkout_date = isset($_GET['checkout']) ? $_GET['checkout'] : (isset($_POST['checkout']) ? $_POST['checkout'] : null);

// Remove arrays from POST before filteration
unset($_POST['features']);
unset($_POST['facilities']);

// Now filter the rest
$filters = filteration($_POST);

// Add back the arrays
$filters['features'] = $features;
$filters['facilities'] = $facilities;

// Base query with JOINs
$query = "SELECT r.*, 
          AVG(rev.rating) as avg_rating,
          COUNT(DISTINCT rev.id) as review_count
          FROM rooms r
          LEFT JOIN reviews rev ON r.id = rev.room_id AND rev.review_status = 'approved'
          WHERE r.status = 1 AND r.removed = 0";

$conditions = [];
$params = [];
$types = "";

// Price filter
if(isset($filters['min_price']) && $filters['min_price'] != ''){
    $conditions[] = "r.price >= ?";
    $params[] = $filters['min_price'];
    $types .= "i";
}

if(isset($filters['max_price']) && $filters['max_price'] != ''){
    $conditions[] = "r.price <= ?";
    $params[] = $filters['max_price'];
    $types .= "i";
}

// Adult capacity filter
if(isset($filters['adults']) && $filters['adults'] != ''){
    $conditions[] = "r.adult >= ?";
    $params[] = $filters['adults'];
    $types .= "i";
}

// Children capacity filter
if(isset($filters['children']) && $filters['children'] != ''){
    $conditions[] = "r.children >= ?";
    $params[] = $filters['children'];
    $types .= "i";
}

// Features filter
if(isset($filters['features']) && !empty($filters['features'])){
    // Convert comma-separated string to array if needed
    $features_array = is_array($filters['features']) ? $filters['features'] : explode(',', $filters['features']);
    $feature_ids = implode(',', array_map('intval', $features_array));
    $conditions[] = "r.id IN (
        SELECT room_id FROM room_features 
        WHERE features_id IN ($feature_ids)
        GROUP BY room_id
        HAVING COUNT(DISTINCT features_id) = " . count($features_array) . "
    )";
}

// Facilities filter
if(isset($filters['facilities']) && !empty($filters['facilities'])){
    // Convert comma-separated string to array if needed
    $facilities_array = is_array($filters['facilities']) ? $filters['facilities'] : explode(',', $filters['facilities']);
    $facility_ids = implode(',', array_map('intval', $facilities_array));
    $conditions[] = "r.id IN (
        SELECT room_id FROM room_facilities 
        WHERE facilities_id IN ($facility_ids)
        GROUP BY room_id
        HAVING COUNT(DISTINCT facilities_id) = " . count($facilities_array) . "
    )";
}

// Add conditions to query
if(!empty($conditions)){
    $query .= " AND " . implode(" AND ", $conditions);
}

// Exclude booked rooms if dates provided
if($checkin_date && $checkout_date){
    $query .= " AND r.id NOT IN (
        SELECT DISTINCT room_id FROM bookings 
        WHERE (booking_status = 'confirmed' OR booking_status = 'completed')
        AND NOT (checkout <= '$checkin_date' OR checkin >= '$checkout_date')
    )";
}

// Group by room
$query .= " GROUP BY r.id";

// Rating filter (using HAVING)
if(isset($filters['min_rating']) && $filters['min_rating'] != ''){
    $query .= " HAVING avg_rating >= " . floatval($filters['min_rating']);
}

// Sorting
if(isset($filters['sort']) && $filters['sort'] != ''){
    if($filters['sort'] == 'price_low'){
        $query .= " ORDER BY r.price ASC";
    } else if($filters['sort'] == 'price_high'){
        $query .= " ORDER BY r.price DESC";
    } else if($filters['sort'] == 'rating'){
        $query .= " ORDER BY avg_rating DESC";
    }
} else {
    $query .= " ORDER BY r.id DESC";
}

// Execute query
if(!empty($params)){
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    $result = mysqli_query($con, $query);
}

$output = "";

if(mysqli_num_rows($result) > 0){
    while($room_data = mysqli_fetch_assoc($result)){
        
        // Get features
        $fea_q = mysqli_query($con, "SELECT f.name FROM features f 
            INNER JOIN room_features rfea ON f.id = rfea.features_id 
            WHERE rfea.room_id = '$room_data[id]'");
        
        $features_data = "";
        while($fea_row = mysqli_fetch_assoc($fea_q)){
            $features_data .= "<span class='badge rounded-pill bg-light text-dark text-wrap me-1 mb-1'>
                $fea_row[name]
            </span>";
        }
        
        // Get facilities
        $fac_q = mysqli_query($con, "SELECT f.name FROM facilities f 
            INNER JOIN room_facilities rfac ON f.id = rfac.facilities_id 
            WHERE rfac.room_id = '$room_data[id]'");
        
        $facilities_data = "";
        while($fac_row = mysqli_fetch_assoc($fac_q)){
            $facilities_data .= "<span class='badge rounded-pill bg-light text-dark text-wrap me-1 mb-1'>
                $fac_row[name]
            </span>";
        }
        
        // Get thumbnail
        $room_thumb = ROOMS_IMG_PATH . "thumbnail.jpg";
        $thumb_q = mysqli_query($con, "SELECT * FROM room_images 
            WHERE room_id='$room_data[id]' AND thumb='1'");
        
        if(mysqli_num_rows($thumb_q) > 0){
            $thumb_res = mysqli_fetch_assoc($thumb_q);
            $room_thumb = ROOMS_IMG_PATH . $thumb_res['image'];
        }
        
        // Rating stars
        $rating_html = "";
        if($room_data['review_count'] > 0){
            $avg_rating = round($room_data['avg_rating'], 1);
            $stars = "";
            for($i = 1; $i <= 5; $i++){
                if($i <= $avg_rating){
                    $stars .= "<i class='bi bi-star-fill text-warning'></i>";
                } else {
                    $stars .= "<i class='bi bi-star text-warning'></i>";
                }
            }
            $rating_html = "<div class='mb-2'>
                $stars
                <span class='badge bg-light text-dark'>$avg_rating / 5</span>
                <small class='text-muted'>($room_data[review_count] reviews)</small>
            </div>";
        }
        
        // Book button
        $book_btn = "";
        $settings_q = mysqli_query($con, "SELECT shutdown FROM settings WHERE sr_no=1");
        $settings_r = mysqli_fetch_assoc($settings_q);
        
        if(!$settings_r['shutdown']){
            $login = (isset($_SESSION['login']) && $_SESSION['login'] == true) ? 1 : 0;
            $book_btn = "<button onclick='checkLoginToBook($login,$room_data[id])' class='btn btn-sm w-100 text-white custom-bg shadow-none mb-2'>Book Now</button>";
        }
        
        $output .= "
        <div class='card mb-4 border-0 shadow'>
            <div class='row g-0 p-3 align-items-center'>
                <div class='col-md-5 mb-lg-0 mb-md-0 mb-3'>
                    <img src='$room_thumb' class='img-fluid rounded'>
                </div>
                <div class='col-md-5 px-lg-3 px-md-3 px-0'>
                    <h5 class='mb-1'>$room_data[name]</h5>
                    $rating_html
                    <div class='features mb-3'>
                        <h6 class='mb-1'>Features</h6>
                        $features_data
                    </div>
                    <div class='facilities mb-3'>
                        <h6 class='mb-1'>Facilities</h6>
                        $facilities_data
                    </div>
                    <div class='guests'>
                        <h6 class='mb-1'>Guests</h6>
                        <span class='badge rounded-pill bg-light text-dark text-wrap'>
                            $room_data[adult] Adults
                        </span>
                        <span class='badge rounded-pill bg-light text-dark text-wrap'>
                            $room_data[children] Children
                        </span>
                    </div>
                </div>
                <div class='col-md-2 mt-lg-0 mt-md-0 mt-4 text-center'>
                    <h6 class='mb-4'>₹$room_data[price] per night</h6>
                    $book_btn
                    <a href='room_details.php?id=$room_data[id]' class='btn btn-sm w-100 btn-outline-dark shadow-none'>More Details</a>
                </div>
            </div>
        </div>";
    }
} else {
    $output = "<div class='alert alert-info text-center'>
        <h5>No rooms found matching your filters!</h5>
        <p>Try adjusting your search criteria.</p>
    </div>";
}

echo $output;

?>
