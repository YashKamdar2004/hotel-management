<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php require('inc/links.php'); ?>
  <title><?php echo $settings_r['site_title'] ?> - MY PROFILE</title>
  <style>
    .profile-img-preview {
      width: 150px;
      height: 150px;
      object-fit: cover;
      border-radius: 50%;
      border: 3px solid #dee2e6;
    }
  </style>
</head>

<body class="bg-light">

  <?php 
    require('inc/header.php');
    
    if(!(isset($_SESSION['login']) && $_SESSION['login'] == true)){
      redirect('index.php');
    }

    $user_res = select("SELECT * FROM `user_cred` WHERE `id`=? LIMIT 1", [$_SESSION['uId']], "i");
    $user_data = mysqli_fetch_assoc($user_res);
  ?>

  <div class="container">
    <div class="row">

      <div class="col-12 my-5 px-4">
        <h2 class="fw-bold">MY PROFILE</h2>
        <div style="font-size: 14px;">
          <a href="index.php" class="text-secondary text-decoration-none">HOME</a>
          <span class="text-secondary"> > </span>
          <a href="#" class="text-secondary text-decoration-none">PROFILE</a>
        </div>
      </div>

      <div class="col-12 px-4">
        <div class="card border-0 shadow-sm mb-4">
          <div class="card-body">
            
            <div id="alert-container"></div>

            <div class="text-center mb-4">
              <img src="<?php echo USERS_IMG_PATH . $user_data['profile']; ?>" 
                   id="profile-preview" 
                   class="profile-img-preview" 
                   alt="Profile Picture">
            </div>

            <form id="profile-form">
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Name</label>
                  <input type="text" name="name" value="<?php echo $user_data['name']; ?>" class="form-control shadow-none" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Email</label>
                  <input type="email" value="<?php echo $user_data['email']; ?>" class="form-control shadow-none" readonly>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Phone Number</label>
                  <input type="text" name="phonenum" value="<?php echo $user_data['phonenum']; ?>" class="form-control shadow-none" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Pincode</label>
                  <input type="text" value="<?php echo $user_data['pincode']; ?>" class="form-control shadow-none" readonly>
                </div>
                <div class="col-md-12 mb-3">
                  <label class="form-label">Address</label>
                  <textarea name="address" class="form-control shadow-none" rows="2" required><?php echo $user_data['address']; ?></textarea>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Date of Birth</label>
                  <input type="date" value="<?php echo $user_data['dob']; ?>" class="form-control shadow-none" readonly>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Profile Picture (Optional)</label>
                  <input type="file" name="profile" accept=".jpg, .jpeg, .png" class="form-control shadow-none" onchange="previewImage(this)">
                </div>
              </div>

              <div class="text-center mt-3">
                <button type="submit" class="btn btn-dark shadow-none">
                  <span id="btn-text">UPDATE PROFILE</span>
                  <span id="btn-loader" class="spinner-border spinner-border-sm d-none"></span>
                </button>
              </div>
            </form>

          </div>
        </div>
      </div>

    </div>
  </div>

  <?php require('inc/footer.php'); ?>

  <script>
    function previewImage(input) {
      if (input.files && input.files[0]) {
        let reader = new FileReader();
        reader.onload = function(e) {
          document.getElementById('profile-preview').src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
      }
    }

    let profile_form = document.getElementById('profile-form');

    profile_form.addEventListener('submit', function(e) {
      e.preventDefault();

      let btn_text = document.getElementById('btn-text');
      let btn_loader = document.getElementById('btn-loader');
      
      btn_text.classList.add('d-none');
      btn_loader.classList.remove('d-none');

      let data = new FormData(this);
      data.append('update_profile', '');

      let xhr = new XMLHttpRequest();
      xhr.open("POST", "ajax/update_profile.php", true);

      xhr.onload = function() {
        btn_text.classList.remove('d-none');
        btn_loader.classList.add('d-none');

        if(this.responseText == 1) {
          alert('success', 'Profile updated successfully!');
          setTimeout(() => {
            window.location.reload();
          }, 1500);
        } else if(this.responseText == 'inv_img') {
          alert('error', 'Only JPG and PNG images are allowed!');
        } else if(this.responseText == 'upd_failed') {
          alert('error', 'Image upload failed!');
        } else if(this.responseText == 'phone_already') {
          alert('error', 'Phone number already registered!');
        } else {
          alert('error', 'Update failed! Please try again.');
        }
      }

      xhr.send(data);
    });
  </script>

</body>
</html>
