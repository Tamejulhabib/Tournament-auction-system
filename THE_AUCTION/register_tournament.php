<?php
include 'db.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Security: Only Players can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Player') {
    header("Location: dashboard.php");
    exit();
}

$player_id = $_SESSION['user_id'];
$message = "";

// Handle Registration Action
if (isset($_POST['join_tournament'])) {
    $t_id = $_POST['t_id'];

    // Check if already registered
    $check = mysqli_query($conn, "SELECT * FROM registrations WHERE player_id = '$player_id' AND tournament_id = '$t_id'");
    
    if (mysqli_num_rows($check) > 0) {
        $message = "<div class='alert alert-warning'>You are already registered for this tournament!</div>";
    } else {
        $sql = "INSERT INTO registrations (player_id, tournament_id) VALUES ('$player_id', '$t_id')";
        if (mysqli_query($conn, $sql)) {
            $message = "<div class='alert alert-success'>Successfully registered! Waiting for Admin approval.</div>";
        } else {
            $message = "<div class='alert alert-danger'>Error: " . mysqli_error($conn) . "</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Tournament Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Available Tournaments</h2>
                <a href="dashboard.php" class="btn btn-secondary btn-sm">Back to Dashboard</a>
            </div>

            <?php echo $message; ?>

            <div class="row">
                <?php
                // Fetch all tournaments created by Admin
                $tournaments = mysqli_query($conn, "SELECT * FROM tournaments WHERE status != 'Completed'");
                
                if (mysqli_num_rows($tournaments) > 0) {
                    while ($t = mysqli_fetch_assoc($tournaments)) {
                        ?>
                        <div class="col-md-6 mb-3">
                            <div class="card shadow-sm border-0">
                                <div class="card-body">
                                    <h5 class="card-title text-primary"><?php echo $t['name']; ?></h5>
                                    <p class="text-muted small">Status: <?php echo $t['status']; ?></p>
                                    <form method="POST">
                                        <input type="hidden" name="t_id" value="<?php echo $t['id']; ?>">
                                        <button type="submit" name="join_tournament" class="btn btn-outline-primary btn-sm w-100">
                                            Register for this Tournament
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    echo "<div class='col-12'><p class='alert alert-info'>No active tournaments available for registration right now.</p></div>";
                }
                ?>
            </div>

            <hr class="my-5">
            <h4>My Registered Tournaments</h4>
            <div class="table-responsive bg-white p-3 rounded shadow-sm">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Tournament Name</th>
                            <th>Date Applied</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $my_regs = mysqli_query($conn, "
                            SELECT tournaments.name, registrations.reg_date, registrations.status 
                            FROM registrations 
                            JOIN tournaments ON registrations.tournament_id = tournaments.id 
                            WHERE registrations.player_id = '$player_id'
                        ");
                        
                        while ($row = mysqli_fetch_assoc($my_regs)) {
                            echo "<tr>
                                    <td>{$row['name']}</td>
                                    <td>{$row['reg_date']}</td>
                                    <td><span class='badge bg-info'>{$row['status']}</span></td>
                                  </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

</body>
</html>