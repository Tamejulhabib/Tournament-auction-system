<?php
include 'db.php';
session_start();

// Security: Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$role = $_SESSION['role'];

// Fetch user email for display
$user_info = mysqli_query($conn, "SELECT email FROM users WHERE id = '$user_id'");
$user_data = mysqli_fetch_assoc($user_info);
$email = $user_data['email'] ?? 'No email found';

// Get Pending Registrations Count for Admin
$pending_count = 0;
if ($role == 'Admin') {
    $count_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM registrations WHERE status = 'Pending'");
    $count_data = mysqli_fetch_assoc($count_query);
    $pending_count = $count_data['total'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - THE_AUCTION</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #f4f7f9; }
        .sidebar { min-height: 100vh; background: #212529; color: white; padding-top: 20px; }
        .nav-link { color: #adb5bd; }
        .nav-link:hover { color: white; }
        .card-icon { font-size: 2rem; margin-bottom: 10px; color: #0d6efd; }
        .badge-notify { position: absolute; top: 10px; right: 10px; }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <nav class="col-md-2 d-none d-md-block sidebar">
            <div class="position-sticky">
                <h5 class="text-center mb-4 text-primary">THE AUCTION</h5>
                <ul class="nav flex-column">
                    <li class="nav-item"><a class="nav-link active" href="dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-left"></i> Logout</a></li>
                </ul>
            </div>
        </nav>

        <main class="col-md-10 ms-sm-auto px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Dashboard</h1>
                <div class="text-muted">Logged in as: <strong><?php echo $username; ?></strong> (<?php echo $role; ?>)</div>
            </div>

            <div class="row g-4">

                <?php if ($role == 'Admin'): ?>
                    <div class="col-md-4">
                        <div class="card shadow-sm text-center p-3 border-0">
                            <i class="bi bi-trophy card-icon"></i>
                            <h5>Create Tournament</h5>
                            <p class="text-muted">Launch a new tournament event.</p>
                            <a href="create_tournament.php" class="btn btn-primary btn-sm">Create Now</a>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card shadow-sm text-center p-3 border-0">
                            <i class="bi bi-person-plus card-icon text-success"></i>
                            <h5>Add Players</h5>
                            <p class="text-muted">Assign players manually to tournaments.</p>
                            <a href="add_player.php" class="btn btn-success btn-sm">Add Player</a>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card shadow-sm text-center p-3 border-0 position-relative">
                            <?php if($pending_count > 0): ?>
                                <span class="badge rounded-pill bg-danger badge-notify"><?php echo $pending_count; ?> Pending</span>
                            <?php endif; ?>
                            <i class="bi bi-check-circle card-icon text-warning"></i>
                            <h5>Approve Registrations</h5>
                            <p class="text-muted">Review and approve player applications.</p>
                            <a href="approve_players.php" class="btn btn-warning btn-sm">Manage Approvals</a>
                        </div>
                    </div>

                <?php elseif ($role == 'Team Owner'): ?>
                    <div class="col-md-4">
                        <div class="card shadow-sm text-center p-3 border-0">
                            <i class="bi bi-shield-shaded card-icon text-success"></i>
                            <h5>Make My Team</h5>
                            <p class="text-muted">Register your official team name.</p>
                            <a href="create_team.php" class="btn btn-success btn-sm">Create Team</a>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card shadow-sm text-center p-3 border-0">
                            <i class="bi bi-people card-icon text-primary"></i>
                            <h5>Auction Pool</h5>
                            <p class="text-muted">View all approved players for the draft.</p>
                            <a href="auction_list.php" class="btn btn-primary btn-sm">View Auction List</a>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card shadow-sm text-center p-3 border-0">
                            <i class="bi bi-list-check card-icon text-secondary"></i>
                            <h5>My Draft</h5>
                            <p class="text-muted">Organize your draft list for auctions.</p>
                            <a href="draft_list.php" class="btn btn-outline-dark btn-sm">Manage Draft</a>
                        </div>
                    </div>

                <?php elseif ($role == 'Player'): ?>
                    <div class="col-md-6">
                        <div class="card shadow-sm text-center p-4 border-0">
                            <i class="bi bi-journal-check card-icon text-warning"></i>
                            <h5>Tournament Registration</h5>
                            <p class="text-muted">Apply for participation in upcoming tournaments.</p>
                            <a href="register_tournament.php" class="btn btn-warning btn-sm">Register Now</a>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card shadow-sm text-center p-4 border-0">
                            <i class="bi bi-person-bounding-box card-icon text-info"></i>
                            <h5>My Profile</h5>
                            <p class="text-muted">Update your stats and profile image.</p>
                            <a href="edit_profile.php" class="btn btn-info btn-sm text-white">Edit Profile</a>
                        </div>
                    </div>
                <?php endif; ?>

            </div>

            <div class="mt-5 mb-5">
                <div class="d-flex justify-content-between">
                    <h4>Current Tournaments</h4>
                </div>
                <div class="table-responsive bg-white shadow-sm p-3 rounded">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Tournament Name</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $res = mysqli_query($conn, "SELECT * FROM tournaments ORDER BY id DESC LIMIT 5");
                            if (mysqli_num_rows($res) > 0) {
                                while($row = mysqli_fetch_assoc($res)) {
                                    $status_class = ($row['status'] == 'Ongoing') ? 'bg-success' : 'bg-info';
                                    echo "<tr>
                                            <td><strong>{$row['name']}</strong></td>
                                            <td><span class='badge {$status_class}'>{$row['status']}</span></td>
                                            <td><a href='view_tournament.php?id={$row['id']}' class='btn btn-sm btn-light border'>Details</a></td>
                                          </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='3' class='text-center text-muted'>No tournaments found.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</div>

</body>
</html>