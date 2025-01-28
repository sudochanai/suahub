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


//Adding user from the Users admin Panel
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add') {

    $firstname = $_POST['firstname'];
    $surname = $_POST['surname'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $gender = $_POST['gender'];
    $password = $surname;

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert into database
    $query = "INSERT INTO users (userfname, usersurname, useremail, usertype, userpass, gender) VALUES ($1, $2, $3, $4, $5,$6)";
    $result = pg_query_params($conn, $query, array($firstname, $surname, $email, $role, $hashedPassword, $gender));


    if ($result) {
        header("Location: users.php");
        exit();
    } else {
        echo "Error: " . pg_last_error($conn);
    }
}

// Handle Edit House Form Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'edit') {
    $firstname = $_POST['firstname'];
    $surname = $_POST['surname'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $gender = $_POST['gender'];

    $query = "UPDATE users SET userfname = '$firstname', usersurname = '$surname', useremail = '$email', usertype = '$role', gender = '$gender' WHERE userid = '$userid'";
    $result = pg_query($conn, $query);

    if ($result) {
        header("Location: users.php");
        exit();
    } else {
        echo "Error: " . pg_last_error($conn);
    }
}

// Handle Delete User Form Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'delete') {
    $userid = $_POST['userid'];

    // Begin transaction
    pg_query($conn, "BEGIN");

    // Delete user from users table
    $deleteUserQuery = "DELETE FROM users WHERE userid = $1";
    $deleteUserResult = pg_query_params($conn, $deleteUserQuery, array($userid));

    if ($deleteUserResult) {
        // Commit transaction
        pg_query($conn, "COMMIT");
        header("Location: users.php");
        exit();
    } else {
        // Rollback transaction if deletion fails
        pg_query($conn, "ROLLBACK");
        echo "Error deleting user: " . pg_last_error($conn);
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
                        <a href="inventory.php" class="nav-link px-3">
                            <span class="me-2"><i class="bi bi-journal-text"></i></span>
                            <span>Invetory</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="nav-link px-3 active">
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
                            <span> <i class='bi bi-table me-2'> </i> </span> Data Table
                        </div>
                        <div class='card-body'>
                            <div class='table-responsive'>
                                <table id='example' class='table table-striped data-table' style='width: 100%'>
                                    <?php
                                    if ($role == 1 || $role == 2) {
                                        echo "<button type='button' class='btn btn-primary' data-bs-toggle='modal' data-bs-target='#addUser'>Add User</button>";
                                    }
                                    ?>

                                    <thead>
                                        <tr>
                                            <th>First Name</th>
                                            <th>Surname</th>
                                            <th>Email</th>
                                            <th>Role</th>
                                            <?php if ($role == 1 || $role == 2) {
                                                echo "<th>Manage</th>";
                                            }
                                            ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $query = "SELECT * FROM users";
                                        $result = pg_query($conn, $query);
                                        while ($row = pg_fetch_assoc($result)) {
                                            echo "<tr>";
                                            echo "<td>{$row['userfname']}</td>";
                                            echo "<td>{$row['usersurname']}</td>";
                                            echo "<td>{$row['useremail']}</td>";
                                            if ($row['usertype'] == 1) {
                                                echo "<td>Admin</td>";
                                            } else if ($row['usertype'] == 2) {
                                                echo "<td>Suahub</td>";
                                            } else {
                                                echo "<td>user</td>";
                                            }
                                            if ($role == 1 || $role == 2) {
                                                echo "<td><button type='submit' class='btn btn-outline-primary editUserBtn' 
                                                data-bs-toggle='modal' data-bs-target='#editUser' data-userid='{$row['userid']}' 
                                                data-userfname='{$row['userfname']}' data-usersurname='{$row['usersurname']}' 
                                                data-useremail='{$row['useremail']}' data-usergender='{$row['gender']}' >
                                                <i class='bi bi-pen'></i>Edit</button>
                                                
                                                <form action='users.php' method='POST' style='display:inline;'>
                                                    <input type='hidden' name='action' value='delete'>
                                                    <input type='hidden' name='userid' value='{$row['userid']}'>
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
            <div class="modal fade" id="addUser">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4> Add User Details </h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="close">
                            </button>
                        </div>
                        <div class="modal-body">
                            <form action="users.php" id="addUser" Method="POST">
                                <input type="hidden" name="action" value="add">
                                <div class="container">
                                    <div>
                                        <label for="name">Name:</label>
                                        <input type="text" class="form-control" name="firstname" id="tenant_fname">
                                    </div>
                                    <div>
                                        <label for="surname">Surname:</label>
                                        <input type="text" class="form-control" name="surname" id="tenant_surname">
                                    </div>
                                    <div>
                                        <label for="email">Email:</label>
                                        <input type="Email" class="form-control" name="email" id="tenant_email">
                                    </div>
                                    <div>
                                        <label for="password">Password:</label>
                                        <input type="password" class="form-control" name="password"
                                            id="tenant_password">
                                    </div>
                                    <div>
                                        <label for="gender">Gender:</label>
                                        <select name="gender" id="userRole" class="form-control">
                                            <option value="" disabled selected>Choose Gender</option>
                                            <option value="M">Male</option>
                                            <option value="F">Female</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="role">Role:</label>
                                        <select name="role" id="userRole" class="form-control">
                                            <option value="" disabled selected>Choose Role</option>
                                            <option value="1">Admin</option>
                                            <option value="2">Suahub</option>
                                            <option value="3">Tenant</option>
                                        </select>
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


            <div class="modal fade" id="editUser">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4> Edit User Details </h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="close">
                            </button>
                        </div>
                        <div class="modal-body">
                            <form action="users.php" id="editUser" method="POST">
                                <input type="hidden" name="action" value="edit">
                                <input type="hidden" name="userid" id="userid">
                                <div class="container">
                                    <div>
                                        <label for="name">Name:</label>
                                        <input type="text" class="form-control" name="firstname" id="userFname">
                                    </div>
                                    <div>
                                        <label for="surname">Surname:</label>
                                        <input type="text" class="form-control" name="surname" id="userSurname">
                                    </div>
                                    <div>
                                        <label for="email">Email:</label>
                                        <input type="Email" class="form-control" name="email" id="userEmail">
                                    </div>
                                    <div>
                                        <label for="gender">Gender:</label>
                                        <select name="gender" id="userRole" class="form-control">
                                            <option value="" disabled selected>Choose Gender</option>
                                            <option value="M">Male</option>
                                            <option value="F">Female</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="role">Role:</label>
                                        <select name="role" id="userRole" class="form-control">
                                        <option value="" disabled selected>Choose Role</option>
                                            <option value="1">Admin</option>
                                            <option value="2">Suahub</option>
                                            <option value="3">Tenant</option>
                                        </select>
                                    </div>
                                    <div class="d-flex h-100">
                                        <div class="align-self-center mx-auto">
                                            <button type="submit" class="btn btn-primary">Edit User</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>"
    <script src="./js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.0.2/dist/chart.min.js"></script>
    <script src="./js/jquery-3.5.1.js"></script>
    <script src="./js/jquery.dataTables.min.js"></script>
    <script src="./js/dataTables.bootstrap5.min.js"></script>
    <script src="./js/script.js"></script>
    <script>
        $(document).ready(function () {
            $('.editUserBtn').on('click', function () {
                var userid = $(this).data('userid');
                var userfname = $(this).data('userfname');
                var usersurname = $(this).data('usersurname');
                var useremail = $(this).data('useremail');
                var userrole = $(this).data('userrole');
                var usergender = $(this).data('usergender');

                $('#userid').val(userid);
                $('#userFname').val(userfname);
                $('#userSurname').val(usersurname);
                $('#userEmail').val(useremail);
                $('#userRole').val(userrole);
                $('#userGender').val(usergender);
            });
        });

    </script>
</body>

</html>