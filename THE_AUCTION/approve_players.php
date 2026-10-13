<?php
include 'db.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: dashboard.php"); exit();
}

// Handle Approval
if (isset($_GET['approve_id'])) {
    $reg_id = $_GET['approve_id'];
    
    // 1. Update registration status
    mysqli_query($conn, "UPDATE registrations SET status = 'Approved' WHERE id = '$reg_id'");
    
    // 2. Add them to the players table for auction (if not already there)
    $info = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM registrations WHERE id = '$reg_id'"));
    $p_id = $info['player_id'];
    $t_id = $info['tournament_id'];
    
    // Fetch user details to populate player table
    $user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT username FROM users WHERE id = '$p_id'"));
    $p_name = $user['username'];

    mysqli_query($conn, "INSERT INTO players (user_id, name, tournament_id, status, base_price) 
                         VALUES ('$p_id', '$p_name', '$t_id', 'Available', '500')");
    
    header("Location: approve_players.php?msg=Approved");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Approve Players - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-4">
    <div class="container">
        <h3>Pending Player Registrations</h3>
        <table class="table table-bordered bg-white shadow-sm mt-3">
            <thead class="table-dark">
                <tr>
                    <th>Player Name</th>
                    <th>Tournament</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $res = mysqli_query($conn, "SELECT r.id, u.username, t.name as tname FROM registrations r 
                                            JOIN users u ON r.player_id = u.id 
                                            JOIN tournaments t ON r.tournament_id = t.id 
                                            WHERE r.status = 'Pending'");
                while($row = mysqli_fetch_assoc($res)) {
                    echo "<tr>
                            <td>{$row['username']}</td>
                            <td>{$row['tname']}</td>
                            <td><a href='approve_players.php?approve_id={$row['id']}' class='btn btn-success btn-sm'>Approve</a></td>
                          </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>