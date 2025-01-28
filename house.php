<?php
include ('db.php');

session_start();

// Check if user is logged in
if (!isset($_SESSION['email'])) {
  header("Location: login.php");
  exit();
}


// Get the current user's privileges and email 
$role = $_SESSION['role'];
$useremail = $_SESSION['email'];
$userid = $_SESSION['userid'];
$username = $_SESSION['username'];

if ($role === 1) {
  $privilege = 'admin';
} else if ($role === 2) {
  $privilege = 'suahub';
} else {
  $privilege = 'user';
}


// Fetch Locations from the database used to add the Locations or even edit 
$locationsQuery = "SELECT locationid, locationname FROM location";
$locationsResult = pg_query($conn, $locationsQuery);
$locations = [];

while ($row = pg_fetch_assoc($locationsResult)) {
  $locations[] = $row;
}

// Handle Add House Form Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add') {
  $houseno = $_POST['houseno'];
  $houserent = $_POST['houserent'];
  $housestatus = $_POST['housestatus'];
  $locationid = $_POST['houselocation'];

  // Start a transaction
  pg_query($conn, "BEGIN");

  // Insert into the house table
  $query = "INSERT INTO house (houseno, houserent, housestatus) VALUES ('$houseno', '$houserent', '$housestatus') RETURNING houseid";
  $result = pg_query($conn, $query);

  if ($result) {
    // Get the houseid of the newly inserted house
    $row = pg_fetch_assoc($result);
    $houseid = $row['houseid'];

    // Insert into the house_location table
    $locationQuery = "INSERT INTO house_location (houseid, houselocation) VALUES ('$houseid', '$locationid')";
    $locationResult = pg_query($conn, $locationQuery);

    if ($locationResult) {
      // Commit the transaction if everything is successful
      pg_query($conn, "COMMIT");
      header("Location: house.php");
      exit();
    } else {
      // Rollback the transaction if there is an error
      pg_query($conn, "ROLLBACK");
      echo "Error inserting into house_location: " . pg_last_error($conn);
    }
  } else {
    // Rollback the transaction if there is an error
    pg_query($conn, "ROLLBACK");
    echo "Error inserting into house: " . pg_last_error($conn);
  }
}



//Editing house from Admin Panel
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'edit') {
  $houseid = $_POST['houseid'];
  $houseno = $_POST['houseno'];
  $houserent = $_POST['houserent'];
  $housestatus = $_POST['housestatus'];
  $locationid = $_POST['houselocation'];

  pg_query($conn, "BEGIN");

  // Update house table
  $query = "UPDATE house SET houseno = '$houseno', houserent = '$houserent', housestatus = '$housestatus' WHERE houseid = '$houseid'";
  $result = pg_query($conn, $query);

  if ($result) {
    // Update house_location table
    $locationQuery = "UPDATE house_location SET houselocation = '$locationid' WHERE houseid = '$houseid'";
    $locationResult = pg_query($conn, $locationQuery);

    if ($locationResult) {
      pg_query($conn, "COMMIT");
      header("Location: house.php");
      exit();
    } else {
      pg_query($conn, "ROLLBACK");
      echo "Error updating house_location: " . pg_last_error($conn);
    }
  } else {
    pg_query($conn, "ROLLBACK");
    echo "Error updating house: " . pg_last_error($conn);
  }
}


///DELETING HOUSE

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'delete') {
  $houseid = $_POST['houseid'];

  // Begin transaction
  pg_query($conn, "BEGIN");

  // Delete inventory from inventory table
  $deleteInventoryQuery = "UPDATE inventory SET house = NULL WHERE house = $1";
  $deleteInventoryResult = pg_query_params($conn, $deleteInventoryQuery, array($houseid));

  $deleteHouseQuery = "DELETE FROM house WHERE houseid = $1";
  $deleteHouseResult = pg_query_params($conn, $deleteHouseQuery, array($houseid));

  if ($deleteHouseResult) {
      // Commit transaction
      pg_query($conn, "COMMIT");
      header("Location: house.php");
      exit();
  } else {
      // Rollback transaction if deletion fails
      pg_query($conn, "ROLLBACK");
      echo "Error deleting house: " . pg_last_error($conn);
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" />
  <link rel="stylesheet" href="css/dataTables.bootstrap5.min.css" />
  <link rel="stylesheet" href="css/bootstrap.min.css" />
  <link rel="stylesheet" href="css/style.css" />
  <title>Dashboard</title>
</head>

<body>
  <!-- top navigation bar -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container-fluid">
      <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebar"
        aria-controls="offcanvasExample">
        <span class="navbar-toggler-icon" data-bs-target="#sidebar"></span>
      </button>
      <a class="navbar-brand me-auto ms-lg-0 ms-3 text-uppercase fw-bold" href="#" >SUAHUB</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#topNavBar"
        aria-controls="topNavBar" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="topNavBar">
        <ul class="navbar-nav d-flex ms-auto my-3 my-lg-0">
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle ms-2" href="#" role="button" data-bs-toggle="dropdown"
              aria-expanded="false">
              <i class="bi bi-person-fill"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item" href="#">Manage Account</a></li>
              <li><a class="dropdown-item" href="logout.php">Logout</a></li>
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </nav>
  <!-- top navigation bar -->
  <!-- offcanvas -->
  <div class="offcanvas offcanvas-start sidebar-nav bg-dark" tabindex="-1" id="sidebar">
    <div class="offcanvas-body p-0">
      <nav class="navbar-dark">
        <ul class="navbar-nav">
          <li>
            <div class="text-muted small fw-bold text-uppercase px-3">
              MENU
            </div>
          </li>
          <li>
            <a href="dashboard.php" class="nav-link px-3">
              <span class="me-2"><i class="bi bi-speedometer2"></i></span>
              <span>Dashboard</span>
            </a>
          </li>
          <li>
            <a href="location.php" class="nav-link px-3">
              <span class="me-2"><i class="bi bi-geo-alt"></i></span>
              <span>Locations</span>
            </a>
          </li>
          <li>
            <a href="#" class="nav-link px-3 active">
              <span class="me-2"><i class="bi bi-house"></i></span>
              <span>Houses</span>
            </a>
          </li>
          <li>
            <a href="inventory.php" class="nav-link px-3">
                <span class="me-2"><i class="bi bi-journal-text"></i></span>
                <span>Inventory</span>
            </a>
          </li>
          <li>
            <a href="users.php" class="nav-link px-3">
              <span class="me-2"><i class="bi bi-people"></i></span>
              <span>Users</span>
            </a>
          </li>
          <li>
            <a href="#" class="nav-link px-3">
              <span class="me-2"><i class="bi bi-cash"></i></span>
              <span>Payment</span>
            </a>
          </li>
          <li>
            <a href="#" class="nav-link px-3">
              <span class="me-2"><i class="bi bi-graph-up"></i></span>
              <span>Reports</span>
            </a>
          </li>
        </ul>
      </nav>
    </div>
  </div>
  <!-- offcanvas -->
  <main class="mt-5 pt-3">
    <div class="container-fluid">
      <div class='row'>
        <div class='col-md-12 mb-3'>
          <div class='card'>
            <div class='card-header'>
              <h4><span> <i class='bi bi-house-fill me-2'> </i> </span> All Houses</h4>
            </div>
            <div class='card-body'>
              <div class='table-responsive'>
                <table id='example' class='table table-striped data-table' style='width: 100%'>
                  <?php
                  if ($role == 1 || $role == 2) {
                    echo "<button type='button' class='btn btn-primary' data-bs-toggle='modal' data-bs-target='#addHouse'>Add House</button>";
                  }
                  ?>
                  <thead>
                    <tr>
                      <th>House No</th>
                      <th>House Rent</th>
                      <th>Rent Status</th>
                      <th>Location</th>
                      
                      <?php if ($role == 1 || $role == 2) {
                        echo "<th>Manage</th>";
                      } ?>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $query = "SELECT h.houseID, h.houseNo, h.houseRent, h.houseStatus, 
                            l.locationName, l.campus, l.block
                            FROM house h
                            JOIN house_location hl ON h.houseID = hl.houseID
                            JOIN location l ON l.locationID = hl.houselocation
                            ";

                    $result = pg_query($conn, $query);
                    
                    while ($row = pg_fetch_assoc($result)) {
                      echo "<tr>";
                      echo "<td>{$row['houseno']}</td>";
                      echo "<td>{$row['houserent']}</td>";
                      echo "<td>{$row['housestatus']}</td>";
                      echo "<td>{$row['locationname']}</td>";
                      if ($role == 1 || $role == 2) {
                        
                        echo "<td>
                        <a href='house_details.php?houseid={$row['houseid']}' class='btn btn-outline-warning' role='button' aria-pressed='true'><i class='bi bi-eye'></i> View</a>

                        <button type='button' class='btn btn-outline-primary editHouseBtn'
                         data-bs-toggle='modal' data-bs-target='#editHouse' data-houseid='{$row['houseid']}' 
                         data-houseno='{$row['houseno']}' data-houserent='{$row['houserent']}' 
                         data-housestatus='{$row['housestatus']}'><i class='bi bi-pen'></i>Edit</button>
                         
                         
                         <form action='house.php' method='POST' style='display:inline;'>
                                     <input type='hidden' name='action' value='delete'>
                                     <input type='hidden' name='houseid' value='{$row['houseid']}'>
                                     <button type='submit' class='btn btn-outline-danger'><i class='bi bi-trash'></i>Delete</button>
                          </form>

                          
                         </td>";
                           
                      }
                      echo "</tr>";
                    }
                    ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
  </main>

  <!-- Add House Modal -->
  <div class="modal fade" id="addHouse" tabindex="-1" aria-labelledby="addHouseLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4>Add House Details</h4>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="close"></button>
        </div>
        <div class="modal-body">
          <form action="house.php" method="POST">
            <input type="hidden" name="action" value="add">
            <div class="container">
              <div>
                <label for="houseno">House Number:</label>
                <input type="text" class="form-control" name="houseno" required>
              </div>
              <div>
                <label for="houserent">House Rent:</label>
                <input type="text" class="form-control" name="houserent" required>
              </div>
              <div>
                <label for="status">Vacancy:</label>
                <select name="housestatus" class="form-control">
                  <option value="" disabled selected>Select Status</option>
                    <option value="Vacant">Vacant</option>
                    <option value="Occupied">Occupied</option>
                </select>
              </div>
              <div>
                <label for="houselocation">Location:</label>
                <select name="houselocation" class="form-control" required>
                  <option value="" disabled selected>Select Location</option>
                  <?php foreach ($locations as $location): ?>
                    <option value="<?php echo $location['locationid']; ?>"><?php echo $location['locationname']; ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="d-flex h-100">
                <div class="align-self-center mx-auto">
                  <button type="submit" class="btn btn-primary">Add House</button>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>


  <!-- Edit House Modal -->
  <div class="modal fade" id="editHouse" tabindex="-1" aria-labelledby="editHouseLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4>Edit House Details</h4>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="close"></button>
        </div>
        <div class="modal-body">
          <form action="house.php" method="POST">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="houseid" id="houseid">
            <div class="container">
              <div>
                <label for="houseno">House Number:</label>
                <input type="text" class="form-control" name="houseno" id="houseno" required>
              </div>
              <div>
                <label for="houserent">House Rent:</label>
                <input type="text" class="form-control" name="houserent" id="houserent" required>
              </div>
              <div>
                <label for="status">Vacancy:</label>
                <select name="housestatus" class="form-control">
                  <option value="" disabled selected>Change Status</option>
                    <option value="Vacant">Vacant</option>
                    <option value="Occupied">Occupied</option>
                </select>
              </div>
              <div>
                <label for="houselocation">Location:</label>
                <select name="houselocation" id="houselocation" class="form-control" required>
                  <option value="" selected disabled>Select Location</option>
                  <?php foreach ($locations as $location): ?>
                    <option value="<?php echo $location['locationid']; ?>"><?php echo $location['locationname']; ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="d-flex h-100">
                <div class="align-self-center mx-auto">
                  <button type="submit" class="btn btn-primary">Edit House</button>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>


  <script src="js/bootstrap.bundle.min.js"></script>
  <script src="js/jquery-3.5.1.js"></script>
  <script src="js/jquery.dataTables.min.js"></script>
  <script src="js/dataTables.bootstrap5.min.js"></script>
  <script src="js/script.js"></script>

  <script>
    $(document).ready(function () {
      $('.editHouseBtn').on('click', function () {
        var houseid = $(this).data('houseid');
        var houseno = $(this).data('houseno');
        var houserent = $(this).data('houserent');
        var housestatus = $(this).data('housestatus');
        var locationid = $(this).data('locationid');

        $('#houseid').val(houseid);
        $('#houseno').val(houseno);
        $('#houserent').val(houserent);
        $('#housestatus').val(housestatus);
        $('#houselocation').val(locationid);
      });
    });
  </script>
</body>

</html>