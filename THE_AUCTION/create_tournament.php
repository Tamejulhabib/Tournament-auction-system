<?php
// 1. DATABASE CONNECTION CHECK
$servername = "localhost";
$db_user = "root";
$db_pass = "";
$dbname = "THE_AUCTION";

$conn = mysqli_connect($servername, $db_user, $db_pass, $dbname);

if (!$conn) {
    die("CRITICAL ERROR: Could not connect to database THE_AUCTION. " . mysqli_connect_error());
}

// 2. SESSION & ADMIN AUTHENTICATION CHECK
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is actually logged in as Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    // If you are seeing this message, your login.php is not setting the session correctly
    die("AUTH ERROR: You are not logged in as Admin. Current Role: " . ($_SESSION['role'] ?? 'None'));
}

$msg = "";

// 3. INSERT LOGIC
if (isset($_POST['submit_tournament'])) {
    $t_name = mysqli_real_escape_string($conn, $_POST['t_name']);
    $t_status = $_POST['t_status'];

    // SQL Query matching your THE_AUCTION table structure
    $sql = "INSERT INTO tournaments (name, status) VALUES ('$t_name', '$t_status')";
    
    if (mysqli_query($conn, $sql)) {
        $msg = "<div class='alert alert-success'>DONE: Tournament '$t_name' saved to THE_AUCTION!</div>";
    } else {
        $msg = "<div class='alert alert-danger'>DATABASE ERROR: " . mysqli_error($conn) . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin: Create Tournament</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="card mx-auto shadow-sm" style="max-width: 600px;">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Admin Tournament Manager</h5>
        </div>
        <div class="card-body">
            <p class="small text-muted">
                Connected to: <strong><?php echo $dbname; ?></strong> | 
                Logged in as: <strong><?php echo $_SESSION['username']; ?></strong>
            </p>
            <hr>

            <?php echo $msg; ?>

            <form method="POST" action="create_tournament.php">
                <div class="mb-3">
                    <label class="form-label">Tournament Name</label>
                    <input type="text" name="t_name" class="form-control" required placeholder="e.g. Asia Cup 2025">
                </div>
                <div class="mb-3">
                    <label class="form-label">Tournament Status</label>
                    <select name="t_status" class="form-select">
                        <option value="Upcoming">Upcoming</option>
                        <option value="Ongoing">Ongoing</option>
                    </select>
                </div>
                <div class="d-grid">
                    <button type="submit" name="submit_tournament" class="btn btn-primary">Create Tournament</button>
                </div>
            </form>
        </div>
        <div class="card-footer text-center">
            <a href="dashboard.php" class="text-decoration-none">← Return to Dashboard</a>
        </div>
    </div>
</div>

</body>
</html>