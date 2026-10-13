<?php
include 'db.php';

// Fix for "session already active" warning
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Security: Only Admin can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: dashboard.php");
    exit();
}

$msg = "";

// Handle Form Submission
if (isset($_POST['reg_player'])) {
    $p_name = mysqli_real_escape_string($conn, $_POST['p_name']);
    $price = $_POST['price'];
    $t_id = $_POST['t_id']; // This is the ID from the dropdown

    // Image Upload Logic
    $img_name = $_FILES['p_img']['name'];
    $tmp_name = $_FILES['p_img']['tmp_name'];
    $folder = "uploads/" . $img_name;

    // Make sure the uploads folder exists
    if (!is_dir('uploads')) {
        mkdir('uploads', 0777, true);
    }

    if (move_uploaded_file($tmp_name, $folder)) {
        // Insert into THE_AUCTION database
        $sql = "INSERT INTO players (name, base_price, image, tournament_id, status) 
                VALUES ('$p_name', '$price', '$img_name', '$t_id', 'Available')";
        
        if (mysqli_query($conn, $sql)) {
            $msg = "<div class='alert alert-success mt-3'>Player Added Successfully!</div>";
        } else {
            $msg = "<div class='alert alert-danger mt-3'>Database Error: " . mysqli_error($conn) . "</div>";
        }
    } else {
        $msg = "<div class='alert alert-danger mt-3'>Failed to upload image. Check folder permissions.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Player - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow border-0">
                <div class="card-header bg-primary text-white py-3 text-center">
                    <h5 class="mb-0">Add New Player</h5>
                </div>
                <div class="card-body p-4">
                    <?php echo $msg; ?>
                    
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Player Name</label>
                            <input type="text" name="p_name" class="form-control" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Base Price</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" name="price" class="form-control" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Select Tournament</label>
                            <select name="t_id" class="form-select" required>
                                <option value="" disabled selected>-- Choose Tournament --</option>
                                <?php
                                // Fetch Tournaments from THE_AUCTION
                                $tournaments = mysqli_query($conn, "SELECT id, name FROM tournaments ORDER BY id DESC");
                                
                                if (mysqli_num_rows($tournaments) > 0) {
                                    while($t = mysqli_fetch_assoc($tournaments)) {
                                        echo "<option value='{$t['id']}'>{$t['name']}</option>";
                                    }
                                } else {
                                    echo "<option value=''>No Tournaments Found - Create one first!</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Player Image</label>
                            <input type="file" name="p_img" class="form-control" accept="image/*" required>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" name="reg_player" class="btn btn-primary">Save Player to Database</button>
                            <a href="dashboard.php" class="btn btn-outline-secondary">Back to Dashboard</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>