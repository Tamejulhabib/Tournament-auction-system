<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Team Owner') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch Team Info
$team_res = mysqli_query($conn, "SELECT id, team_name FROM teams WHERE owner_id = '$user_id'");
$team = mysqli_fetch_assoc($team_res);

// FIX: Check if team exists before accessing the array
if (!$team) {
    echo "<script>alert('Please create a team first!'); window.location.href='create_team.php';</script>";
    exit();
}

$team_id = $team['id'];
$team_name = $team['team_name'];

// Handle Remove from Draft
if (isset($_GET['remove'])) {
    $d_id = $_GET['remove'];
    mysqli_query($conn, "DELETE FROM draft_selections WHERE id = '$d_id' AND team_id = '$team_id'");
    header("Location: draft_list.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>My Draft List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center">
            <h3>My Draft List: <span class="text-primary"><?php echo $team_name; ?></span></h3>
            <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>
        
        <div class="card shadow-sm mt-3">
            <div class="card-body">
                <table class="table table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Player Name</th>
                            <th>Base Price</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT ds.id as draft_id, p.name, p.base_price 
                                FROM draft_selections ds 
                                JOIN players p ON ds.player_id = p.id 
                                WHERE ds.team_id = '$team_id'";
                        $res = mysqli_query($conn, $sql);
                        
                        if (mysqli_num_rows($res) > 0) {
                            while($row = mysqli_fetch_assoc($res)) {
                                echo "<tr>
                                        <td>{$row['name']}</td>
                                        <td>\${$row['base_price']}</td>
                                        <td class='text-center'>
                                            <a href='draft_list.php?remove={$row['draft_id']}' class='btn btn-danger btn-sm'>Remove</a>
                                        </td>
                                      </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='3' class='text-center text-muted'>No players in your draft list yet.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>