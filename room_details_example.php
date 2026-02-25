<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php 
    require('inc/links.php'); 
    require('inc/review_functions.php');
  ?>
  <title><?php echo $settings_r['site_title'] ?> - ROOM DETAILS</title>
</head>

<body class="bg-light">

  <?php require('inc/header.php'); ?>

  <?php
    // Example: Get room ID from URL
    if(!isset($_GET['id'])){
      redirect('rooms.php');
    }
    
    $room_id = filteration($_GET)['id'];
    
    // Fetch room data (your existing code)
    $room_res = select("SELECT * FROM `rooms` WHERE `id`=? AND `status`=? AND `removed`=?", [$room_id, 1, 0], 'iii');
    
    if(mysqli_num_rows($room_res) == 0){
      redirect('rooms.php');
    }
    
    $room_data = mysqli_fetch_assoc($room_res);
    
    // Get room rating
    $rating_data = get_room_rating($room_id);
  ?>

  <div class="container my-5">
    <div class="row">
      
      <!-- Room Details Section (Your existing code here) -->
      <div class="col-12 mb-4">
        <h2><?php echo $room_data['name']; ?></h2>
        
        <!-- Display Average Rating -->
        <?php if($rating_data['total_reviews'] > 0): ?>
          <div class="mb-3">
            <?php echo display_stars($rating_data['avg_rating']); ?>
            <span class="fw-bold"><?php echo $rating_data['avg_rating']; ?> / 5</span>
            <span class="text-muted">(based on <?php echo $rating_data['total_reviews']; ?> reviews)</span>
          </div>
        <?php endif; ?>
        
        <p>Price: ₹<?php echo $room_data['price']; ?> per night</p>
        <!-- Add your other room details here -->
      </div>

      <!-- Reviews Section -->
      <div class="col-12">
        <h4 class="mb-4">Guest Reviews</h4>
        
        <?php
          $reviews = get_room_reviews($room_id);
          
          if(mysqli_num_rows($reviews) > 0){
            while($review = mysqli_fetch_assoc($reviews)){
              $review_date = date("d M Y", strtotime($review['created_at']));
              ?>
              <div class="card mb-3 shadow-sm">
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0"><?php echo $review['user_name']; ?></h6>
                    <small class="text-muted"><?php echo $review_date; ?></small>
                  </div>
                  <div class="mb-2">
                    <?php echo display_stars($review['rating']); ?>
                  </div>
                  <p class="mb-0"><?php echo $review['review']; ?></p>
                </div>
              </div>
              <?php
            }
          } else {
            echo "<p class='text-muted'>No reviews yet. Be the first to review!</p>";
          }
        ?>
      </div>

    </div>
  </div>

  <?php require('inc/footer.php'); ?>

</body>
</html>
