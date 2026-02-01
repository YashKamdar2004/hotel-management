<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SkyLine Suites - ROOM DETAILS</title>

  <?php require('inc/links.php'); ?>

  <style>
    .h-line{
      width: 150px;
      margin: 0 auto;
      height: 1.7px;
      background-color: #000 !important;
    }
  </style>
</head>

<body class="bg-light">

  <?php require('inc/header.php');?>

  <?php

    if(!isset($_GET['id'])){
      redirect('rooms.php');
    }

    $data = filteration($_GET);

    $room_res = select("SELECT * FROM `rooms` WHERE `id`=? AND `status`=? AND `removed`=?",[$data['id'],1,0],'iii');

    if(mysqli_num_rows($room_res)==0){
      redirect('rooms.php');
    }

    $room_data = mysqli_fetch_assoc($room_res);
  ?>

  <div class="container">
    <div class="row">

      <div class="col-12 my-5 mb-4 px-4">
        <h2 class="fw-bold"><?php echo $room_data['name'] ?></h2>
        <div style="font-size: 14px;">
          <a href="index.php" class="text-secondary text-decoration-none">HOME</a>
          <span class="text-secondary"> > </span>
          <a href="rooms.php" class="text-secondary text-decoration-none">ROOMS</a>
        </div>
      </div>

      <div class="col-lg-7 col-md-12 px-4">
        <div id="roomCarousel" class="carousel slide" data-bs-ride="carousel">
          <div class="carousel-inner">
            <?php

              $room_img = ROOMS_IMG_PATH."thumbnail.jpg";

              $img_q = mysqli_query($con,"SELECT * FROM `room_images` 
                WHERE `room_id`='$room_data[id]'");

              if(mysqli_num_rows($img_q)>0)
              {
                $active_class='active'; 

                while($img_res = mysqli_fetch_assoc($img_q))
                {
                  echo " <div class='carousel-item $active_class'>
                      <img src='".ROOMS_IMG_PATH.$img_res['image']."' class='d-block w-100 rounded'>
                    </div>
                  ";

                  $active_class='';
                  
                }

              }
              else{
                echo " <div class='carousel-item active'>
                  <img src='$room_img' class='d-block w-100'>
                </div>";
              }

            ?>
          </div>
          <button class="carousel-control-prev" type="button" data-bs-target="#roomCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
          </button>
          <button class="carousel-control-next" type="button" data-bs-target="#roomCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
          </button>
        </div>
        
      </div>

      <div class="col-lg-9 col-md-12 px-4">

        <?php

         /*  $room_res = select("SELECT * FROM `rooms` WHERE `status`=? AND `removed`=?",[1,0],'ii');

          while($room_data = mysqli_fetch_assoc($room_res))
          {
            $fea_q = mysqli_query($con,"SELECT f.name FROM `features` f 
              INNER JOIN `room_features` rfea ON f.id = rfea.features_id 
              WHERE rfea.room_id = '$room_data[id]'");

            $features_data = "";

            while($fea_row = mysqli_fetch_assoc($fea_q)){
              $features_data .="<span class='badge rounded-pill bg-light text-dark text-wrap'>
                  $fea_row[name]
                </span>";
            }

            $fac_q = mysqli_query($con,"SELECT f.name FROM `facilities` f 
              INNER JOIN `room_facilities` rfac ON f.id = rfac.facilities_id 
              WHERE rfac.room_id = '$room_data[id]'");

            $facilities_data = "";

            while($fac_row = mysqli_fetch_assoc($fac_q)){
              $facilities_data .="<span class='badge rounded-pill bg-light text-dark text-wrap'>
                  $fac_row[name]
                </span>";
            }

            $room_thumb = ROOMS_IMG_PATH."thumbnail.jpg";

            $thumb_q = mysqli_query($con,"SELECT * FROM `room_images` 
              WHERE `room_id`='$room_data[id]' 
              AND `thumb`=' 1'");

            if(mysqli_num_rows($thumb_q)>0)
            {
              $thumb_res = mysqli_fetch_assoc($thumb_q);
              $room_thumb = ROOMS_IMG_PATH.$thumb_res['image'];
            }

            echo <<<data

            <div class="card mb-4 border-0 shadow">
              <div class="row g-0 p-3 align-items-center">

                <div class="col-md-5 mb-lg-0 mb-md-0 mb-3">
                  <img src="$room_thumb" class="img-fluid rounded">
                </div>

                <div class="col-md-5 px-lg-3 px-md-3 px-0">
                  <h5 class="mb-3">$room_data[name]</h5>

                  <div class="features mb-3">
                    <h6 class="mb-1">Features</h6>
                    $features_data
                  </div>

                  <div class="facilities mb-3">
                    <h6 class="mb-1">Facilities</h6>
                    $facilities_data
                  </div>

                  <div class="guests">
                    <h6 class="mb-1">Guests</h6>
                    <span class="badge rounded-pill bg-light text-dark text-wrap">
                      $room_data[adult] Adults
                    </span>
                    <span class="badge rounded-pill bg-light text-dark text-wrap">
                      $room_data[children] Children
                    </span>
                  </div>
                </div>

                <div class="col-md-2 mt-lg-0 mt-md-0 mt-4 text-center">
                  <h6 class="mb-4">₹$room_data[price] per night</h6>
                  <a href="#" class="btn btn-sm w-100 text-white custom-bg shadow-none mb-2">Book Now</a>
                  <a href="room_details.php?id=$room_data[id]" class="btn btn-sm w-100 btn-outline-dark shadow-none">More Details</a>
                </div>

              </div>
            </div>

            data;

          } */

        ?>

      </div>

    </div>
  </div>

  <?php require('inc/footer.php'); ?>

</body>
</html>
