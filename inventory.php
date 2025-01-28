 <?php
include('db.php');

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

$privilege = $role === 1 ? 'admin' : ($role === 2 ? 'suahub' : 'user');

// Handle Add Inventory Form Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add') {
    $invname = $_POST['invname'];
    $invno = $_POST['invno'];
    $invtype = $_POST['invtype'];

    pg_query($conn, "BEGIN");
    $query = "INSERT INTO inventory (invname, invno, invtype, house) VALUES ($1, $2, $3, NULL)";
    $result = pg_query_params($conn, $query, array($invname, $invno, $invtype));

    if ($result) {
        pg_query($conn, "COMMIT");
        header("Location: inventory.php");
        exit();
    } else {
        pg_query($conn, "ROLLBACK");
        echo "Error inserting into inventory: " . pg_last_error($conn);
    }
}

// Handle Assign House Form Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'assign_house') {
    $invid = $_POST['invid'];
    $housename = $_POST['houseid'];

    pg_query($conn, "BEGIN");

    // Retrieve the houseid using the houseno
    $selectHouseidQuery = "SELECT houseid FROM house WHERE houseno = $1";
    $houseResult = pg_query_params($conn, $selectHouseidQuery, array($housename));

    if ($houseResult && pg_num_rows($houseResult) > 0) {
        $houseRow = pg_fetch_assoc($houseResult);
        $houseid = $houseRow['houseid'];

        // Update the inventory with the selected houseid
        $updateQuery = "UPDATE inventory SET house = $1 WHERE invid = $2";
        $updateResult = pg_query_params($conn, $updateQuery, array($houseid, $invid));

        if ($updateResult) {
            pg_query($conn, "COMMIT");
            header("Location: inventory.php");
            exit();
        } else {
            pg_query($conn, "ROLLBACK");
            echo "Error updating inventory: " . pg_last_error($conn);
        }
    } else {
        pg_query($conn, "ROLLBACK");
        echo "Error: House not found.";
    }
}


// Fetch Allocated Inventories
$allocatedQuery = "SELECT i.*, h.houseno FROM inventory i JOIN house h ON i.house = h.houseid";
$allocatedResult = pg_query($conn, $allocatedQuery);

// Fetch Unallocated Inventories
$unallocatedQuery = "SELECT * FROM inventory WHERE house IS NULL";
$unallocatedResult = pg_query($conn, $unallocatedQuery);

// Fetch All Houses for Autocomplete
$housesQuery = "SELECT * FROM house";
$housesResult = pg_query($conn, $housesQuery);
$houses = [];
while ($row = pg_fetch_assoc($housesResult)) {
    $houses[] = ['houseid' => $row['houseid'], 'houseno' => $row['houseno']];
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css"
    />
    <link rel="stylesheet" href="css/dataTables.bootstrap5.min.css" />
    <link rel="stylesheet" href="css/bootstrap.min.css" />
    <link rel="stylesheet" href="css/style.css" />
    <title>Dashboard</title>
  </head>
  <body>
<!-- top navigation bar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
      <div class="container-fluid">
        <button
          class="navbar-toggler"
          type="button"
          data-bs-toggle="offcanvas"
          data-bs-target="#sidebar"
          aria-controls="offcanvasExample"
        >
          <span class="navbar-toggler-icon" data-bs-target="#sidebar"></span>
        </button>
        <a
          class="navbar-brand me-auto ms-lg-0 ms-3 text-uppercase fw-bold"
          href="#"
          >SUAHUB</a
        >
        <button
          class="navbar-toggler"
          type="button"
          data-bs-toggle="collapse"
          data-bs-target="#topNavBar"
          aria-controls="topNavBar"
          aria-expanded="false"
          aria-label="Toggle navigation"
        >
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="topNavBar">
          
          <ul class="navbar-nav d-flex ms-auto my-3 my-lg-0">
            <li class="nav-item dropdown">
              <a
                class="nav-link dropdown-toggle ms-2"
                href="#"
                role="button"
                data-bs-toggle="dropdown"
                aria-expanded="false"
              >
                <i class="bi bi-person-fill"></i>
              </a>
              <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="#">Manage Account</a></li>
                <li><a class="dropdown-item" href="logout.php">Logut</a></li>
              </ul>
            </li>
          </ul>
        </div>
      </div>
    </nav>
    <!-- top navigation bar -->
    <!-- offcanvas -->
    <div
      class="offcanvas offcanvas-start sidebar-nav bg-dark"
      tabindex="-1"
      id="sidebar"
    >
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
              <a href="house.php" class="nav-link px-3">
                <span class="me-2"><i class="bi bi-house"></i></span>
                <span>Houses</span>
              </a>
            </li>
            <li>
            <a href="inventory.php" class="nav-link px-3 active">
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
            <div class="row">
                <div class="col-md-12 mb-3">
                    <div class="card">
                        <div class="card-header">
                            <h4><i class="bi bi-journal-text"></i>Inventory Management</h4>
                        </div>
                        <div class="card-body">
                            <?php if ($role == 1 || $role == 2): ?>
                            <a type="submit" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addInventory">Add Inventory</a>
                            <?php endif; ?>
                            <div class="table-responsive">
                                <h5>Allocated Inventory</h5>
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Inventory Name</th>
                                            <th>Inventory Number</th>
                                            <th>Inventory Type</th>
                                            <th>House Number</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = pg_fetch_assoc($allocatedResult)): ?>
                                        <tr>
                                            <td><?php echo $row['invname']; ?></td>
                                            <td><?php echo $row['invno']; ?></td>
                                            <td><?php echo $row['invtype']; ?></td>
                                            <td><?php echo $row['houseno']; ?></td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="table-responsive">
                                <h5>Unallocated Inventory</h5>
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Inventory Name</th>
                                            <th>Inventory Number</th>
                                            <th>Inventory Type</th>
                                            <th>Assign House</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = pg_fetch_assoc($unallocatedResult)): ?>
                                        <tr>
                                            <td><?php echo $row['invname']; ?></td>
                                            <td><?php echo $row['invno']; ?></td>
                                            <td><?php echo $row['invtype']; ?></td>
                                            <td>
                                                <form method="post" action="inventory.php">
                                                    <input type="hidden" name="invid" value="<?php echo $row['invid']; ?>">
                                                    <input type="text" class="form-control house-autocomplete" name="houseid" placeholder="Enter house number">
                                                    <button type="submit" class="btn btn-primary mt-2" name="action" value="assign_house">Assign</button>
                                                </form>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Add Inventory Modal -->
    <div class="modal fade" id="addInventory" tabindex="-1" aria-labelledby="addInventoryLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addInventoryLabel">Add Inventory</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" action="inventory.php">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="invname" class="form-label">Inventory Name</label>
                            <input type="text" class="form-control" id="invname" name="invname" required>
                        </div>
                        <div class="mb-3">
                            <label for="invno" class="form-label">Inventory Number</label>
                            <input type="text" class="form-control" id="invno" name="invno" required>
                        </div>
                        <div class="mb-3">
                            <label for="invtype" class="form-label">Inventory Type</label>
                            <input type="text" class="form-control" id="invtype" name="invtype" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="action" value="add">Add Inventory</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- End Add Inventory Modal -->

    <!-- JavaScript Libraries -->
    <script src="./js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.0.2/dist/chart.min.js"></script>
    <script src="./js/jquery-3.5.1.js"></script>
    <script src="./js/jquery.dataTables.min.js"></script>
    <script src="./js/dataTables.bootstrap5.min.js"></script>
    <script src="./js/script.js"></script>
    <script>
        // Autocomplete for house numbers
        $(function () {
            var houses = <?php echo json_encode($houses); ?>;
            $(".house-autocomplete").autocomplete({
                source: houses.map(function (house) {
                    return {
                        label: house.houseno,
                        value: house.houseid
                    };
                }),
                minLength: 1,
                select: function (event, ui) {
                    $(this).val(ui.item.label);
                    $(this).siblings('input[name="houseid"]').val(ui.item.value);
                    return false;
                }
            });
        });
    </script>
</body>
</html>
