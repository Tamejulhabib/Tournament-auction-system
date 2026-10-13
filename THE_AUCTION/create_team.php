<?php
include 'db.php';
session_start();

// Security: Only Team Owners can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Team Owner') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";

// 1. Check if this Owner already has a team
$check_team = mysqli_query($conn, "SELECT * FROM teams WHERE owner_id = '$user_id'");
$my_team = mysqli_fetch_assoc($check_team);

// 2. Handle Team Registration
if (isset($_POST['submit_team'])) {
    $team_name = mysqli_real_escape_string($conn, $_POST['team_name']);
    $initial_budget = 10000.00; // You can set any starting budget

    if (!$my_team) {
        $sql = "INSERT INTO teams (owner_id, team_name, budget) VALUES ('$user_id', '$team_name', '$initial_budget')";
        if (mysqli_query($conn, $sql)) {
            $message = "<div class='alert alert-success text-center'>Team '$team_name' registered successfully!</div>";
            // Refresh team info
            $check_team = mysqli_query($conn, "SELECT * FROM teams WHERE owner_id = '$user_id'");
            $my_team = mysqli_fetch_assoc($check_team);
        } else {
            $message = "<div class='alert alert-danger'>Error: " . mysqli_error($conn) . "</div>";
        }
    } else {
        $message = "<div class='alert alert-warning'>You already have a team registered.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Team - THE AUCTION</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-success text-white text-center py-3">
                    <h4 class="mb-0"><i class="bi bi-shield-check"></i> Team Registration</h4>
                </div>
                <div class="card-body p-4">
                    <?php echo $message; ?>

                    <?php if ($my_team): ?>
                        <div class="text-center py-3">
                            <h5 class="text-muted">Your Current Team</h5>
                            <h2 class="text-success mb-3"><?php echo $my_team['team_name']; ?></h2>
                            <div class="p-3 bg-light rounded mb-3">
                                <p class="mb-1 text-muted">Available Budget</p>
                                <h3 class="fw-bold text-dark">$<?php echo number_format($my_team['budget'], 2); ?></h3>
                            </div>
                            <a href="dashboard.php" class="btn btn-outline-secondary w-100 mt-2">Go to Dashboard</a>
                        </div>

                    <?php else: ?>
                        <p class="text-muted text-center small">Register your official team name to start drafting players.</p>
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Team Name</label>
                                <input type="text" name="team_name" class="form-control" placeholder="e.g. Dhaka Gladiators" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Starting Budget</label>
                                <input type="text" class="form-control" value="$10,000.00" disabled>
                                <small class="text-muted">Standard budget for all new owners.</small>
                            </div>
                            <div class="d-grid mt-4">
                                <button type="submit" name="submit_team" class="btn btn-success py-2">Create My Team</button>
                                <a href="dashboard.php" class="btn btn-link text-muted mt-2">Back to Dashboard</a>
                            </div>
                        </form>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>