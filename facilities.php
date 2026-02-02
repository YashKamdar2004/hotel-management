<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php require('inc/links.php'); ?>
  <title><?php echo $settings_r['site_title'] ?> - FACILITIES</title>

  <style>
    .pop:hover{
      border-top-color: var(--teal) !important;
      transform: scale(1.03);
      transition: all 0.3
    }

    .h-line{
    width: 150px;
    margin: 0 auto;
    height: 1.7px;
    background-color: #000 !important;
  }
  </style>

</head>
<body class="bg-light">

<!-- including header -->

<?php require('inc/header.php');?>

<div class="my-5 px-4">
  <h2 class="fw-bold h-font text-center">OUR FACILITIES</h2>
  <div class="h-line"></div>
  <p class="text-center mt-3">
    Lorem ipsum dolor sit amet consectetur, adipisicing elit.
    Atque dolorem quam <br> doloremque quae esse temporibus 
    aspernatur, recusandae est omnis nemo!
  </p>
</div>

<div class="container">
  <div class="row">

  <?php

    $res = selectAll('facilities');
    $path = FACILITIES_IMG_PATH;

    while($row = mysqli_fetch_assoc($res)){
      echo<<<data
        <div class="col-lg-4 col-md-6 mb-5 px-4">
          <div class="bg-white rounded shadow p-4 border-top border-4 border-dark pop">
            <div class="d-flex align-items-center mb-2">
              <img src="$path$row[icon]" width="40px">
              <h5 class="m-0 ms-3">$row[name]</h5>
            </div>
            <p>$row[description]</p>
          </div>
        </div>
      data;
    }

  ?>  
    
  </div>
</div>

<!-- including footer -->

<?php require('inc/footer.php'); ?>

</body>
</html>