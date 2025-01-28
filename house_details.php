<?php
include('db.php');

session_start();

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

/////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
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
/////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
// Get the houseid from the URL
$houseid = isset($_GET['houseid']) ? intval($_GET['houseid']) : null;

if (!$houseid) {
    echo "No house selected.";
    exit();
}
/////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
// Fetch inventory related to the selected house
$query = "SELECT * FROM inventory WHERE house = $1";
$result = pg_query_params($conn, $query, array($houseid));

if (!$result) {
    echo "Query error: " . pg_last_error($conn);
    exit();
}

$inventoryItems = [];
while ($row = pg_fetch_assoc($result)) {
    $inventoryItems[] = $row;
}
/////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
// Handle Add Inventory Form Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add') {
    $invname = $_POST['invname'];
    $invno = $_POST['invno'];
    $invtype = $_POST['invtype'];

    // Start a transaction
    pg_query($conn, "BEGIN");

    // Insert into the inventory table
    $query = "INSERT INTO inventory (invname, invno, invtype, house) VALUES ($1, $2, $3, $4)";
    $result = pg_query_params($conn, $query, array($invname, $invno, $invtype, $houseid));

    if ($result) {
        // Commit the transaction if everything is` successful
        pg_query($conn, "COMMIT");
        header("Location: house_details.php?houseid=$houseid");
        exit();
    } else {
        // Rollback the transaction if there is an error
        pg_query($conn, "ROLLBACK");
        echo "Error inserting into inventory: " . pg_last_error($conn);
    }
}
/////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
// Handle Edit Inventory Form Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'edit') {
    $invid = $_POST['invid'];
    $invname = $_POST['invname'];
    $invno = $_POST['invno'];
    $invtype = $_POST['invtype'];

    // Start a transaction
    pg_query($conn, "BEGIN");

    $query = "UPDATE inventory SET invno = $1, invname = $2, invtype = $3 WHERE invid = $4";
    $result = pg_query_params($conn, $query, array($invno, $invname, $invtype, $invid));

    if ($result) {
        pg_query($conn, "COMMIT");
        header("Location: house_details.php?houseid=$houseid");
        exit();
    } else {
        pg_query($conn, "ROLLBACK");
        echo "Error updating inventory: " . pg_last_error($conn);
    }
}
/////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
// Handle Delete Inventory Form Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'delete') {
    $invid = $_POST['invid'];

    // Begin transaction
    pg_query($conn, "BEGIN");

    // Update inventory from the inventory table
    $deleteInventoryQuery = "UPDATE inventory SET house = NULL WHERE invid = $1";
    $deleteInventoryResult = pg_query_params($conn, $deleteInventoryQuery, array($invid));

    if ($deleteInventoryResult) {
        // Commit transaction
        pg_query($conn, "COMMIT");
        header("Location: house_details.php?houseid=$houseid");
        exit();
    } else {
        // Rollback transaction if deletion fails
        pg_query($conn, "ROLLBACK");
        echo "Error deleting inventory: " . pg_last_error($conn);
    }
}
/////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////

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
                        <a href="house.php" class="nav-link px-3 active">
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
                            <h4><span> <i class="bi bi-pencil"></i> </span> Contracts Management</h4>
                        </div>
                        <div class='card-body'>
                            <table class='table table-striped table-hover' id='contractsTable'>
                                <button class='btn btn-success' id='addContractBtn'>Add Contract</button>
                                <thead>
                                    <tr>
                                        <th>Contract ID</th>
                                        <th>Contract Name</th>
                                        <th>Contract Type</th>
                                        <th>Contract Description</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($contractItems)): ?>
                                        <?php foreach ($contractItems as $contract): ?>
                                            <tr>
                                                <td><?php echo $contract['contractid']; ?></td>
                                                <td><?php echo $contract['contractname']; ?></td>
                                                <td><?php echo $contract['contracttype']; ?></td>
                                                <td><?php echo $contract['contractdescription']; ?></td>
                                                <td>
                                                    <button class='btn btn-primary edit-btn'
                                                        data-contract-id='<?php echo $contract['contractid']; ?>'>Edit</button>
                                                    <button class='btn btn-danger delete-btn'
                                                        data-contract-id='<?php echo $contract['contractid']; ?>'>Delete</button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5">No contracts found for this house.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>

                            </table>

                        </div>
                    </div>
                </div>
            </div>


            <div class='row'>
                <div class='col-md-12 mb-3'>
                    <div class='card'>
                        <div class='card-header'>
                            <h4><span> <i class='bi bi-house-fill me-2'> </i> </span> House Inventory</h4>
                        </div>
                        <div class='card-body'>
                            <div class='table-responsive'>
                                <table id='example' class='table table-striped data-table' style='width: 100%'>
                                    <?php
                                    if ($role == 1 || $role == 2) {
                                        echo "<a type='submit' class='btn btn-primary' data-bs-toggle='modal' data-bs-target='#addInventory'>Add Inventory</a>";
                                    }
                                    ?>
                                    <thead>
                                        <tr>
                                            <th>Inventory Name</th>
                                            <th>Inventory Number</th>
                                            <th>Inventory Type</th>
                                            <?php
                                            if ($role == 1 || $role == 2) {
                                                echo "<th>Actions</th>";
                                            }
                                            ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        foreach ($inventoryItems as $item) {
                                            echo "<tr>";
                                            echo "<td>{$item['invname']}</td>";
                                            echo "<td>{$item['invno']}</td>";
                                            echo "<td>{$item['invtype']}</td>";
                                            if ($role == 1 || $role == 2) {
                                                echo "<td>
                                                        <form method='post' action='house_details.php?houseid=$houseid'>
                                                            <input type='hidden' name='invid' value='{$item['invid']}' />
                                                            <button type='button' class='btn btn-outline-primary' data-bs-toggle='modal' 
                                                            data-bs-target='#editInventory' data-id='{$item['invid']}' 
                                                            data-name='{$item['invname']}' data-no='{$item['invno']}' 
                                                            data-type='{$item['invtype']}'><i class='bi bi-pen'></i>Edit</button>

                                                            <button type='submit' name='action' value='delete' class='btn btn-outline-danger'>
                                                            <i class='bi bi-trash'></i>Delete</button>
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
                <form method="post" action="house_details.php?houseid=<?php echo $houseid; ?>">
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

    <!-- Edit Inventory Modal -->
    <div class="modal fade" id="editInventory" tabindex="-1" aria-labelledby="editInventoryLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editInventoryLabel">Edit Inventory</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" action="house_details.php?houseid=<?php echo $houseid; ?>">
                    <div class="modal-body">
                        <input type="hidden" id="invid" name="invid">
                        <div class="mb-3">
                            <label for="editInvname" class="form-label">Inventory Name</label>
                            <input type="text" class="form-control" id="editInvname" name="invname" required>
                        </div>
                        <div class="mb-3">
                            <label for="editInvno" class="form-label">Inventory Number</label>
                            <input type="text" class="form-control" id="editInvno" name="invno" required>
                        </div>
                        <div class="mb-3">
                            <label for="editInvtype" class="form-label">Inventory Type</label>
                            <input type="text" class="form-control" id="editInvtype" name="invtype" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="action" value="edit">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Contract Modal -->
    <div class="modal fade" id="addContract" tabindex="-1" aria-labelledby="addContractLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editInventoryLabel">Add Contract</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" action="house_details.php?houseid=<?php echo $houseid; ?>">
                    <div class="modal-body">
                        <input type="hidden" id="invid" name="invid">
                        <div class="mb-3">
                            <label for="editInvname" class="form-label">Inventory Name</label>
                            <input type="text" class="form-control" id="editInvname" name="invname" required>
                        </div>
                        <div class="mb-3">
                            <label for="editInvno" class="form-label">Inventory Number</label>
                            <input type="text" class="form-control" id="editInvno" name="invno" required>
                        </div>
                        <div class="mb-3">
                            <label for="editInvtype" class="form-label">Inventory Type</label>
                            <input type="text" class="form-control" id="editInvtype" name="invtype" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="action" value="edit">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/jquery.min.js"></script>
    <script src="js/dataTables.min.js"></script>
    <script src="js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#example').DataTable();

            $('#editInventory').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var id = button.data('id');
                var name = button.data('name');
                var no = button.data('no');
                var type = button.data('type');

                var modal = $(this);
                modal.find('#invid').val(id);
                modal.find('#editInvname').val(name);
                modal.find('#editInvno').val(no);
                modal.find('#editInvtype').val(type);
            });
        });
    </script>
</body>

</html>