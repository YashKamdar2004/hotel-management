<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php require('inc/links.php'); ?>
  <title><?php echo $settings_r['site_title'] ?> - ABOUT US</title>

  <!-- swiperjs file -->

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.css" />

  <Style>
  .h-line{
  width: 150px;
  margin: 0 auto;
  height: 1.7px;
  background-color: #000 !important;
  }

  .box{
    border-top-color: var(--teal) !important;
  }
  </style>

</head>
<body class="bg-light">

<!-- including header -->

<?php require('inc/header.php');?>

<div class="my-5 px-4">
  <h2 class="fw-bold h-font text-center">ABOUT US</h2>
  <div class="h-line"></div>
  <p class="text-center mt-3">
    <?php echo nl2br(htmlspecialchars($settings_r['site_about'])); ?>
  </p>
</div>

<div class="container">
  <div class="row justify-content-between align-items-center">
    <?php
      // Fetch additional about data if columns exist
      $about_query = "SELECT * FROM `settings` WHERE `sr_no` = 1";
      $about_res = mysqli_query($con, $about_query);
      $about_data = mysqli_fetch_assoc($about_res);
      
      // Check if optional columns exist
      $has_mission = isset($about_data['mission']) && $about_data['mission'] != '';
      $has_vision = isset($about_data['vision']) && $about_data['vision'] != '';
      $has_image = isset($about_data['about_image']) && $about_data['about_image'] != '';
      
      // Use about_image if exists, otherwise use default
      $about_img = $has_image ? ABOUT_IMG_PATH . htmlspecialchars($about_data['about_image']) : 'images/about/about.jpg';
    ?>
    
    <div class="col-lg-6 col-md-5 mb-4 order-lg-1 order-md-1 order-2">
      <h3 class="mb-3">Welcome to <?php echo htmlspecialchars($settings_r['site_title']); ?></h3>
      <p><?php echo nl2br(htmlspecialchars($settings_r['site_about'])); ?></p>
      
      <?php if($has_mission): ?>
      <div class="mt-4">
        <h5 class="fw-bold"><i class="bi bi-bullseye me-2"></i>Our Mission</h5>
        <p><?php echo nl2br(htmlspecialchars($about_data['mission'])); ?></p>
      </div>
      <?php endif; ?>
      
      <?php if($has_vision): ?>
      <div class="mt-4">
        <h5 class="fw-bold"><i class="bi bi-eye me-2"></i>Our Vision</h5>
        <p><?php echo nl2br(htmlspecialchars($about_data['vision'])); ?></p>
      </div>
      <?php endif; ?>
    </div>
    
    <div class="col-lg-5 col-md-5 mb-4 order-lg-2 order-md-2 order-1">
      <img src="<?php echo $about_img; ?>" class="w-100 rounded shadow" alt="About Us">
    </div>
  </div>
</div>

<div class="container mt-5">
  <div class="row">
    <div class="col-lg-3 col-md-6 mb-4 px-4">
      <div class="bg-white rounded shadow p-4 border-top border-4 text-center box">
        <img src="images/about/hotel.svg" width="70px">
        <h4 class="mt-3">100+ ROOMS</h4>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-4 px-4">
      <div class="bg-white rounded shadow p-4 border-top border-4 text-center box">
        <img src="images/about/customers.svg" width="70px">
        <h4 class="mt-3">200+ CUSTOMERS</h4>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-4 px-4">
      <div class="bg-white rounded shadow p-4 border-top border-4 text-center box">
        <img src="images/about/rating.svg" width="70px">
        <h4 class="mt-3">150+ REVIEWS</h4>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-4 px-4">
      <div class="bg-white rounded shadow p-4 border-top border-4 text-center box">
        <img src="images/about/staff.svg" width="70px">
        <h4 class="mt-3">100+ STAFFS</h4>
      </div>
    </div>
  </div>
</div>

<h3 class="my-5 fw-bold h-font text-center">MANAGEMENT TEAM</h3>

<div class="container px-4">
  <?php
    // Fetch all team members (no status filter since column doesn't exist)
    $team_query = "SELECT * FROM `team_details` ORDER BY `sr_no` ASC";
    $team_res = mysqli_query($con, $team_query);
    
    if(mysqli_num_rows($team_res) > 0){
      echo '<div class="swiper mySwiper">
        <div class="swiper-wrapper mb-5">';
      
      $path = ABOUT_IMG_PATH;
      while($row = mysqli_fetch_assoc($team_res)){
        $team_name = htmlspecialchars($row['name']);
        $team_pic = htmlspecialchars($row['picture']);
        
        echo <<<data
          <div class="swiper-slide bg-white text-center overflow-hidden rounded">
            <img src="$path$team_pic" class="w-100" alt="$team_name">
            <h5 class="mt-2">$team_name</h5>
          </div>
        data;
      }
      
      echo '</div>
        <div class="swiper-pagination"></div>
      </div>';
    } else {
      echo '<div class="text-center">
        <p class="text-muted fs-5">Our team details will be updated soon.</p>
      </div>';
    }
  ?>
</div>


<!-- including footer -->

<?php require('inc/footer.php'); ?>


<!-- Swiper JS -->
  <script src="https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.js"></script>

<!-- Initialize Swiper -->
<script>
  var swiper = new Swiper(".mySwiper", {
    spaceBetween: 40,
    pagination: {
      el: ".swiper-pagination",
    },
    breakpoints:{
      320:{
        slidesPerView: 1,
      },
      640:{
        slidesPerView: 1,
      },
      768:{
        slidesPerView: 3,
      },
      1024:{
        slidesPerView: 3,
      },
    }
  });
</script>

</body>
</html>