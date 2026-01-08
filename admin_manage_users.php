<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}


include 'connect.php';

// Search functionality
$search_query = "";
if (isset($_GET['search'])) {
    $search_query = mysqli_real_escape_string($conn, $_GET['search']);
    $query = "SELECT * FROM students WHERE name LIKE '%$search_query%' OR id_number LIKE '%$search_query%' OR status LIKE '%$search_query%'";
} else {
    $query = "SELECT * FROM students";
}

$result = mysqli_query($conn, $query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h2 class="mb-3">Manage Users</h2>
        
        <!-- Search Bar -->
        <form method="GET" action="admin_manage_users.php" class="mb-3">
            <input type="text" name="search" value="<?= htmlspecialchars($search_query); ?>" 
                   class="form-control" placeholder="Search by Name, ID Number, or Status">
        </form>

        <!-- User Table -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID Number</th>
                    <th>Name</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?= $row['id_number']; ?></td>
                        <td><?= $row['name']; ?></td>
                        <td><?= ucfirst($row['status']); ?></td>
                        <td>
                            <!-- Enable / Disable -->
                            <?php if ($row['status'] == 'active') { ?>
                                <a href="admin_manage_users.php?action=disable&user_id=<?= $row['user_id']; ?>" 
                                   class="btn btn-warning btn-sm"
                                   onclick="return confirm('Are you sure you want to disable this user?');">Disable</a>
                            <?php } else { ?>
                                <a href="admin_manage_users.php?action=enable&user_id=<?= $row['user_id']; ?>" 
                                   class="btn btn-success btn-sm">Enable</a>
                            <?php } ?>

                            <!-- Delete User -->
                            <a href="admin_manage_users.php?action=delete&user_id=<?= $row['user_id']; ?>" 
                               class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this user?');">
                                Delete
                            </a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <div class="row">
    <div class="col-md-4">
        <div class="widget">
            <div class="title">Total Users</div>
            <div class="count"><?php echo $total_users; ?></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="widget">
            <div class="title">Active Users</div>
            <div class="count"><?php echo $active_users; ?></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="widget">
            <div class="title">Disabled Users</div>
            <div class="count"><?php echo $disabled_users; ?></div>
        </div>
    </div>
</div>

<!-- Add Export Button -->
<div class="text-end">
    <a href="export_reports.php" class="btn btn-primary">Export Reports to Excel</a>
</div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
