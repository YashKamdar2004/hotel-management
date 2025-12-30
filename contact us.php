<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SkyLine Suites - CONTACT US</title>

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

<!-- including header -->

<?php require('inc/header.php');?>

<div class="my-5 px-4">
  <h2 class="fw-bold h-font text-center">CONTACT US</h2>
  <div class="h-line"></div>
  <p class="text-center mt-3">
    Lorem ipsum dolor sit amet consectetur, adipisicing elit.
    Atque dolorem quam <br> doloremque quae esse temporibus 
    aspernatur, recusandae est omnis nemo!
  </p>
</div>

<div class="container">
  <div class="row">
    <div class="col-lg-6 col-md-6 mb-5 px-4">

      <div class="bg-white rounded shadow p-4">
        <iframe class="w-100 rounded mb-4" height="320px" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d236294.8194967101!2d70.72656717313568!3d22.27395315722296!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3959b46477b75f8b%3A0x8cbae52fb37adb10!2sRajkot%2C%20Gujarat!5e0!3m2!1sen!2sin!4v1766761344026!5m2!1sen!2sin" height="450" loading="lazy"></iframe>

        <h5>Address</h5>
        <a href="https://maps.app.goo.gl/VzWbbXjmwZyJMkNu6" target="_blank" class="d-inline-block text-decoration none text-dark mb-2">
          <i class="bi bi-geo-alt-fill"></i> XYZ, Rajkot, Gujarat
        </a>

        <h5 class="mt-4">Call Us</h5>
        <a href="tel: +917777924727" class="d-inline-block mb-2 text-decoration-none text-dark">
          <i class="bi bi-telephone-fill"></i> +917777924727
        </a>
        <br>
        <a href="tel: +917777924727" class="d-inline-block text-decoration-none text-dark">
          <i class="bi bi-telephone-fill"></i> +917777924727
        </a>

        <h5 class="mt-4">Email</h5>
        <a href="mailto: yashkamdar872@gmail.com" class="d-inline-block text-decoration-none text-dark">
          <i class="bi bi-envelope-fill"></i>  ask yashkamdar872@gmail.com
        </a>

        <h5 class="mt-4">Follow Us</h5>
        <a href="#" class="d-inline-block text-dark fs-5 me-2">
          <i class="bi bi-twitter-x me-1"></i>
          </span>
        </a>
        <a href="#" class="d-inline-block text-dark fs-5 me-2">
          <i class="bi bi-facebook me-1"></i>
        </a>
        <a href="#" class="d-inline-block text-dark fs-5">
          <i class="bi bi-instagram me-1"></i>
        </a>
      </div>
    </div>
    <div class="col-lg-6 col-md-6 px-4">
      <div class="bg-white rounded shadow p-4">
        <form>
          <h5>Send a Message</h5>
          <div class="mt-3">
            <label class="form-label" style="font-weight: 500;">Name</label>
            <input type="text" class="form-control shadow-none">
          </div>
          <div class="mt-3">
            <label class="form-label" style="font-weight: 500;">Email</label>
            <input type="email" class="form-control shadow-none">
          </div>
          <div class="mt-3">
            <label class="form-label" style="font-weight: 500;">Subject</label>
            <input type="text" class="form-control shadow-none">
          </div>
          <div class="mt-3">
            <label class="form-label" style="font-weight: 500;">Message</label>
            <textarea class="form-control shadow-none" rows="5" style="resize: none;"></textarea>
          </div>
          <button type="submit" class="btn text-white custom-bg mt-3">SEND</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- including footer -->

<?php require('inc/footer.php'); ?>

</body>
</html>