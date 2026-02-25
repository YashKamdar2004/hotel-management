<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php require('inc/links.php'); ?>
  <title><?php echo $settings_r['site_title'] ?> - ROOMS</title>

  <style>
    .h-line{
    width: 150px;
    margin: 0 auto;
    height: 1.7px;
    background-color: #000 !important;
  }
  #rooms-loader {
    display: none;
    text-align: center;
    padding: 50px;
  }
  </style>

</head>
<body class="bg-light">

<!-- including header -->

<?php require('inc/header.php');?>

<div class="my-5 px-4">
  <h2 class="fw-bold h-font text-center">OUR ROOMS</h2>
  <div class="h-line"></div>
  
  <?php
    // Display availability search info if parameters exist
    if(isset($_GET['checkin']) && isset($_GET['checkout'])){
      $checkin = htmlspecialchars($_GET['checkin']);
      $checkout = htmlspecialchars($_GET['checkout']);
      $adults = isset($_GET['adults']) ? htmlspecialchars($_GET['adults']) : 1;
      $children = isset($_GET['children']) ? htmlspecialchars($_GET['children']) : 0;
      
      echo "<div class='alert alert-info text-center mt-3'>
        <strong>Showing available rooms for:</strong><br>
        Check-in: " . date('d M Y', strtotime($checkin)) . " | 
        Check-out: " . date('d M Y', strtotime($checkout)) . " | 
        Guests: $adults Adult(s), $children Child(ren)
      </div>";
    }
  ?>
</div>

<div class="container-fluid">
  <div class="row">

      <div class="col-lg-3 col-md-12 mb-lg-0 mb-4 ps-4">
        <nav class="navbar navbar-expand-lg navbar-light bg-white rounded shadow">
          <div class="container-fluid flex-lg-column align-items-stretch">
            <h4 class="mt-2">FILTERS</h4>
            <button class="navbar-toggler shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#filterDropdown" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
              <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse flex-column align-items-stretch mt-2" id="filterDropdown">
              
              <!-- Price Range Filter -->
              <div class="border bg-light p-3 rounded mb-3">
                <h5 class="mb-3" style="font-size: 18px;">PRICE RANGE</h5>
                <div class="d-flex">
                  <div class="me-2">
                    <label class="form-label">Min</label>
                    <input type="number" id="min_price" class="form-control shadow-none" placeholder="Min">
                  </div>
                  <div>
                    <label class="form-label">Max</label>
                    <input type="number" id="max_price" class="form-control shadow-none" placeholder="Max">
                  </div>
                </div>
              </div>

              <!-- Rating Filter -->
              <div class="border bg-light p-3 rounded mb-3">
                <h5 class="mb-3" style="font-size: 18px;">MINIMUM RATING</h5>
                <select id="min_rating" class="form-select shadow-none">
                  <option value="">Any Rating</option>
                  <option value="5">5 Stars</option>
                  <option value="4">4+ Stars</option>
                  <option value="3">3+ Stars</option>
                  <option value="2">2+ Stars</option>
                  <option value="1">1+ Stars</option>
                </select>
              </div>

              <!-- Features Filter -->
              <div class="border bg-light p-3 rounded mb-3">
                <h5 class="mb-3" style="font-size: 18px;">FEATURES</h5>
                <?php
                  $features_res = selectAll('features');
                  while($feature = mysqli_fetch_assoc($features_res)){
                    echo "<div class='mb-2'>
                      <input type='checkbox' id='feature$feature[id]' name='features' value='$feature[id]' class='form-check-input shadow-none me-1'>
                      <label class='form-check-label' for='feature$feature[id]'>$feature[name]</label>
                    </div>";
                  }
                ?>
              </div>

              <!-- Facilities Filter -->
              <div class="border bg-light p-3 rounded mb-3">
                <h5 class="mb-3" style="font-size: 18px;">FACILITIES</h5>
                <?php
                  $facilities_res = selectAll('facilities');
                  while($facility = mysqli_fetch_assoc($facilities_res)){
                    echo "<div class='mb-2'>
                      <input type='checkbox' id='facility$facility[id]' name='facilities' value='$facility[id]' class='form-check-input shadow-none me-1'>
                      <label class='form-check-label' for='facility$facility[id]'>$facility[name]</label>
                    </div>";
                  }
                ?>
              </div>

              <!-- Guests Filter -->
              <div class="border bg-light p-3 rounded mb-3">
                <h5 class="mb-3" style="font-size: 18px;">GUESTS</h5>
                <div class="d-flex">
                  <div class="me-3">
                    <label class="form-label">Adults</label>
                    <input type="number" id="adults" class="form-control shadow-none" min="0">
                  </div>
                  <div>
                    <label class="form-label">Children</label>
                    <input type="number" id="children" class="form-control shadow-none" min="0">
                  </div>
                </div>
              </div>

              <!-- Sort Filter -->
              <div class="border bg-light p-3 rounded mb-3">
                <h5 class="mb-3" style="font-size: 18px;">SORT BY</h5>
                <select id="sort" class="form-select shadow-none">
                  <option value="">Default</option>
                  <option value="price_low">Price: Low to High</option>
                  <option value="price_high">Price: High to Low</option>
                  <option value="rating">Highest Rated</option>
                </select>
              </div>

              <!-- Action Buttons -->
              <button class="btn btn-dark shadow-none mb-2" onclick="applyFilters()">Apply Filters</button>
              <button class="btn btn-outline-secondary shadow-none" onclick="clearFilters()">Clear Filters</button>

            </div>
          </div>
        </nav>
      </div>

    <!-- cards -->

      <div class="col-lg-9 col-md-12 px-4">

        <!-- Loading Spinner -->
        <div id="rooms-loader">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
          <p class="mt-2">Loading rooms...</p>
        </div>

        <!-- Rooms Container -->
        <div id="rooms-container">
          <!-- Rooms will be loaded here via AJAX -->
        </div>

      </div>

  </div>
</div>


<!-- including footer -->

<?php require('inc/footer.php'); ?>

<script>
  function applyFilters() {
    let loader = document.getElementById('rooms-loader');
    let container = document.getElementById('rooms-container');
    
    loader.style.display = 'block';
    container.innerHTML = '';
    
    let formData = new FormData();
    
    // Price filters
    let min_price = document.getElementById('min_price').value;
    let max_price = document.getElementById('max_price').value;
    if(min_price) formData.append('min_price', min_price);
    if(max_price) formData.append('max_price', max_price);
    
    // Rating filter
    let min_rating = document.getElementById('min_rating').value;
    if(min_rating) formData.append('min_rating', min_rating);
    
    // Features filter
    let features = [];
    document.querySelectorAll('input[name="features"]:checked').forEach(cb => {
      features.push(cb.value);
    });
    if(features.length > 0) {
      features.forEach(f => formData.append('features[]', f));
    }
    
    // Facilities filter
    let facilities = [];
    document.querySelectorAll('input[name="facilities"]:checked').forEach(cb => {
      facilities.push(cb.value);
    });
    if(facilities.length > 0) {
      facilities.forEach(f => formData.append('facilities[]', f));
    }
    
    // Guests filter
    let adults = document.getElementById('adults').value;
    let children = document.getElementById('children').value;
    if(adults) formData.append('adults', adults);
    if(children) formData.append('children', children);
    
    // Sort filter
    let sort = document.getElementById('sort').value;
    if(sort) formData.append('sort', sort);
    
    let xhr = new XMLHttpRequest();
    xhr.open('POST', 'ajax/filter_rooms.php', true);
    
    xhr.onload = function() {
      loader.style.display = 'none';
      container.innerHTML = this.responseText;
    };
    
    xhr.send(formData);
  }
  
  function clearFilters() {
    document.getElementById('min_price').value = '';
    document.getElementById('max_price').value = '';
    document.getElementById('min_rating').value = '';
    document.getElementById('adults').value = '';
    document.getElementById('children').value = '';
    document.getElementById('sort').value = '';
    
    document.querySelectorAll('input[name="features"]').forEach(cb => cb.checked = false);
    document.querySelectorAll('input[name="facilities"]').forEach(cb => cb.checked = false);
    
    applyFilters();
  }
  
  // Load all rooms on page load
  window.onload = function() {
    // Check if availability parameters exist in URL
    const urlParams = new URLSearchParams(window.location.search);
    const checkin = urlParams.get('checkin');
    const checkout = urlParams.get('checkout');
    const adults = urlParams.get('adults');
    const children = urlParams.get('children');
    
    // If availability params exist, apply them
    if(checkin && checkout && adults) {
      document.getElementById('adults').value = adults;
      if(children) document.getElementById('children').value = children;
    }
    
    applyFilters();
  };
</script>

</body>
</html>