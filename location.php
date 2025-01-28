<?php
include ('db.php');

session_start();
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$role = $_SESSION['role'];
$useremail = $_SESSION['email'];
$userid = $_SESSION['userid'];
$username = $_SESSION['username'];

if ($role === 1) {
    $privilege = 'admin';
} else if ($role === 2) {
    $privilege = 'suahub';
} else {
    $privilege = 'tenant';
}

//Handle Adding Location to database
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add') {

    $locationname = $_POST['name'];
    $campus = $_POST['campus'];
    $block = $_POST['block'];

    $query = "INSERT INTO location(locationname, campus, block) VALUES('$locationname', '$campus', '$block')";
    $result = pg_query($conn, $query);

    if ($result) {
        header("Location: location.php");
        exit();
    } else {
        echo "Error: " . pg_last_error($conn);
    }
}

// Handle Edit House Form Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'edit') {
    $locationid = $_POST['locationid'];
    $locationname = $_POST['name'];
    $campus = $_POST['campus'];
    $block = $_POST['block'];

    $query = "UPDATE location SET locationname = '$locationname', campus = '$campus', block = '$block' WHERE locationid = '$locationid'";
    $result = pg_query($conn, $query);

    if ($result) {
        header("Location: location.php");
        exit();
    } else {
        echo "Error: " . pg_last_error($conn);
    }
}


//Handle Deletion of   
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'delete') {
    $locationid = $_POST['locationid'];

    $query = "DELETE FROM location WHERE locationid = $1";
    $result = pg_query_params($conn, $query, array($locationid));

    if ($result) {
        echo "Location deleted successfully.";
    } else {
        echo "Error: " . pg_last_error($conn);
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
            <a class="navbar-brand me-auto ms-lg-0 ms-3 text-uppercase fw-bold" href="#">SUAHUB</a>
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
                            <li><a class="dropdown-item" href="logout.php">Logut</a></li>
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
                            CORE
                        </div>
                    </li>
                    <li>
                        <a href="dashboard.php" class="nav-link px-3 ">
                            <span class="me-2"><i class="bi bi-speedometer2"></i></span>
                            <span>Dashboard</span>
                        </a>
                    </li>

                    <li>
                        <a href="#" class="nav-link px-3 active">
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

<!-- Main Dashboard Area-->
    <main class="mt-5 pt-3">
        <div class="container-fluid">
            
            <div class='row'>
                <div class='col-md-12 mb-3'>
                    <div class='card'>
                        <div class='card-header'>
                        <h4> <span> <i class='bi bi-geo-alt me-2'> </i> </span>House Location</h4>
                        </div>
                        <div class='card-body'>
                            <div class='table-responsive'>
                                <table id='example' class='table table-striped data-table' style='width: 100%'>
                                    <?php
                                    if ($role == 1 || $role == 2) {
                                        echo "<button type='button' class='btn btn-primary' data-bs-toggle='modal' data-bs-target='#addLocation'>Add Location</button>";
                                    }
                                    ?>
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Campus</th>
                                            <th>Block</th>
                                            <?php if ($role == 1 || $role == 2) {
                                                echo "<th>Manage</th>";
                                            }
                                            ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $query = "SELECT * FROM location";
                                        $result = pg_query($conn, $query);
                                        while ($row = pg_fetch_assoc($result)) {
                                        echo "<tr>";
                                            echo "<td>{$row['locationname']}</td>";
                                            echo "<td>{$row['campus']}</td>";
                                            echo "<td>{$row['block']}</td>";
                                            if ($role == 1 || $role == 2) {
                                                echo "<td><button type='submit' class='btn btn-outline-primary editLocationBtn' 
                                                data-bs-toggle='modal' data-bs-target='#editLocation' data-locationid='{$row['locationid']}'
                                                 data-locationname='{$row['locationname']}' data-campus='{$row['campus']}' data-block='{$row['block']}'>
                                                 <i class='bi bi-pen'></i>Edit</button>
                                                 
                                                 <form action='location.php' method='POST' style='display:inline;'>
                                                         <input type='hidden' name='action' value='delete'>
                                                         <input type='hidden' name='locationid' value='{$row['locationid']}'>
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
            <div class="modal fade" id="addLocation">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4> Add Location Details </h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="close">
                            </button>
                        </div>
                        <div class="modal-body">
                            <form action="location.php" id="addLocation" Method="POST">
                                <input type="hidden" name="action" value="add">
                                <div class="container">
                                    <div>
                                        <label for="name">Name:</label>
                                        <input type="text" class="form-control" name="name" id="location_name">
                                    </div>
                                    <div>
                                        <label for="name">Campus:</label>
                                        <input type="text" class="form-control" name="campus" id="location_campus">
                                    </div>
                                    <div>
                                        <label for="name">Block:</label>
                                        <input type="text" class="form-control" name="block" id="location_block">
                                    </div>
                                    <div class="d-flex h-100">
                                        <div class="align-self-center mx-auto">
                                            <button type="submit" class="btn btn-primary">Add Location</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>


            <div class="modal fade" id="editLocation">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4> Edit Location Details </h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="close">
                            </button>
                        </div>
                        <div class="modal-body">
                            <form action="location.php" id="editLocation" method="POST">
                                <input type="hidden" name="action" value="edit">
                                <input type="hidden" name="locationid" id="locationid">
                                <div class="container">
                                    <div>
                                        <label for="name">Name:</label>
                                        <input type="text" class="form-control" name="name" id="locationname">
                                    </div>
                                    <div>
                                        <label for="name">Campus:</label>
                                        <input type="text" class="form-control" name="campus" id="campus">
                                    </div>
                                    <div>
                                        <label for="name">Block:</label>
                                        <input type="text" class="form-control" name="block" id="block">
                                    </div>
                                    <div class="d-flex h-100">
                                        <div class="align-self-center mx-auto">
                                            <button type="submit" class="btn btn-primary">Edit Location</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- End of main-->

    <script src="./js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.0.2/dist/chart.min.js"></script>
    <script src="./js/jquery-3.5.1.js"></script>
    <script src="./js/jquery.dataTables.min.js"></script>
    <script src="./js/dataTables.bootstrap5.min.js"></script>
    <script src="./js/script.js"></script>
    <script>
        $(document).ready(function () {
            $('.editLocationBtn').on('click', function () {
                var locationid = $(this).data('locationid');
                var locationname = $(this).data('locationname');
                var campus = $(this).data('campus');
                var block = $(this).data('block');

                $('#locationid').val(locationid);
                $('#locationname').val(locationname);
                $('#campus').val(campus);
                $('#block').val(block);
            });
        });
    </script>
</body>

</html>