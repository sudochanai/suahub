<?php
session_start();
if (isset($_SESSION['error_message'])) {
  $error_message = $_SESSION['error_message'];
  unset($_SESSION['error_message']);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" />
  <link rel="stylesheet" href="css/dataTables.bootstrap5.min.css" />
  <link rel="stylesheet" href="css/bootstrap.min.css" />
  <link rel="stylesheet" href="css/style.css" />
  <title>Login</title>
</head>

<body>
  <section class="vh-100">
    <div class="container-fluid h-custom">
      <div class="row d-flex justify-content-center align-items-center h-100">
        <div class="col-md-9 col-lg-6 col-xl-5">
        <h1 style="font-size:60px;">Login to SUAHUB</h1>
          <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-login-form/draw2.webp" class="img-fluid"
            alt="Sample image">
        </div>
        <div class="col-md-8 col-lg-6 col-xl-4 offset-xl-1" style="border: 1px grey solid; border-radius:8px;">
          <form method="POST" action="login_action.php" style="margin: 40px;">
            <br>

            <?php
            if (isset($error_message)) {
              echo '<div class="alert alert-danger" role="alert">' . $error_message . '</div>';
            }
            ?>

            <!-- Email input -->
            <div data-mdb-input-init class="form-outline mb-4">
              <input type="email" id="form3Example3" class="form-control form-control-lg" name="useremail"
                placeholder="Enter a valid email address" required />
            </div>

            <!-- Password input -->
            <div data-mdb-input-init class="form-outline mb-3">
              <input type="password" id="form3Example4" class="form-control form-control-lg" name="userpass"
                placeholder="Enter password" required />
            </div>

            <div class="d-flex justify-content-between align-items-center">
              <!-- Checkbox -->
              <div class="form-check mb-0">
                <input class="form-check-input me-2" type="checkbox" value="" id="form2Example3" />
                <label class="form-check-label" for="form2Example3">
                  Remember me
                </label>
              </div>
              <a href="#!" class="text-body">Forgot password?</a>
            </div>

            <div class="text-center text-lg-start mt-4 pt-2">
              <button type="submit" data-mdb-button-init data-mdb-ripple-init class="btn btn-primary btn-lg"
                style="padding-left: 2.5rem; padding-right: 2.5rem;">Login</button>
              <p class="small fw-bold mt-2 pt-1 mb-0">Don't have an account? <a href="register.php"
                  class="link-danger">Register</a></p>
            </div>

          </form>
        </div>
      </div>
    </div>
    <div
      class="d-flex flex-column flex-md-row text-center text-md-start justify-content-between py-4 px-4 px-xl-5 bg-success">
      <!-- Copyright -->
      <div class="text-white mb-3 mb-md-0">
        Copyright © 2020. All rights reserved.
      </div>
      <!-- Right -->
      <div>
        <a href="#!" class="text-white me-4">
          <i class="fab fa-facebook-f"></i>
        </a>
        <a href="#!" class="text-white me-4">
          <i class="fab fa-twitter"></i>
        </a>
        <a href="#!" class="text-white me-4">
          <i class="fab fa-google"></i>
        </a>
        <a href="#!" class="text-white">
          <i class="fab fa-linkedin-in"></i>
        </a>
      </div>
    </div>
  </section>
</body>

</html>