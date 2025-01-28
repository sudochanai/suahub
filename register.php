<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" />
  <link rel="stylesheet" href="css/dataTables.bootstrap5.min.css" />
  <link rel="stylesheet" href="css/bootstrap.min.css" />
  <link rel="stylesheet" href="css/style.css" />
  <title>Registration</title>
</head>

<body>
  <section class="vh-100">
    <div class="container-fluid h-custom">
      <div class="row d-flex justify-content-center align-items-center h-100">
        <div class="col-md-9 col-lg-6 col-xl-5">
          <h1 style="font-size:60px;">Register to SUAHUB</h1>
          <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-login-form/draw2.webp" class="img-fluid"
            alt="Sample image">
        </div>
        <div class="col-md-8 col-lg-6 col-xl-4 offset-xl-1" style="border: 1px grey solid; border-radius:8px;">
          <form method="POST" action="register_action.php" style="margin: 40px;">

            <?php
            session_start();
            if (isset($_SESSION['error_message'])) {
              $error_message = $_SESSION['error_message'];
              unset($_SESSION['error_message']);
              echo '<div class="alert alert-danger" role="alert">' . $error_message . '</div>';
            }
            ?>
            <!-- First Name and Last Name -->
            <div class="row">

              <div class="col-md-6 mb-4">
                <div data-mdb-input-init class="form-outline">
                  <input type="text" id="firstName" class="form-control form-control-lg" name="userfname"
                    placeholder="First Name" required />
                </div>
              </div>
              <div class="col-md-6 mb-4">
                <div data-mdb-input-init class="form-outline">
                  <input type="text" id="lastName" class="form-control form-control-lg" name="usersurname"
                    placeholder="Last Name" required />
                </div>
              </div>
            </div>

            <!-- Gender -->
            <div class="form-outline mb-4">
              <select name="gender" id="userRole" class="form-control form-control-lg">
                <option value="">Select Gender</option>
                <option value="M">Male</option>
                <option value="F">Female</option>
              </select>
            </div>

            <!-- Email -->
            <div class="form-outline mb-4">
              <input type="email" id="emailAddress" class="form-control form-control-lg" name="useremail"
                placeholder="Email" required />
            </div>

            <!-- Password and Confirm Password -->
            <div class="form-outline mb-4">
              <input type="password" id="userPass" class="form-control form-control-lg" name="userPass"
                placeholder="Password" required />
            </div>
            <div class="form-outline mb-4">
              <input type="password" id="confirmPass" class="form-control form-control-lg" name="confirmPass"
                placeholder="Confirm Password" required />
            </div>

            <!-- Submit Button -->
            <div class="text-center text-lg-start mt-4 pt-2">
              <input data-mdb-ripple-init class="btn btn-primary btn-lg" type="submit" value="Submit"
                style="padding-left: 2.5rem; padding-right: 2.5rem;" />
              <p class="small fw-bold mt-2 pt-1 mb-0">Already have an account? <a href="login.php"
                  class="link-danger">Login</a></p>
            </div>

          </form>
        </div>
      </div>
    </div>

    <!-- Footer -->
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