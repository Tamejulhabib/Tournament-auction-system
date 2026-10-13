<?php
include 'db.php';
session_start();

$user_id = $_SESSION['user_id'];
// Get Owner's Team ID
$team_res = mysqli_query($conn, "SELECT id FROM teams WHERE owner_id = '$user_id'");
$team_data = mysqli_fetch_assoc($team_res);
$team_id = $team_data['id'] ?? null;

// Handle Adding to Draft
if (isset($_GET['add_to_draft'])) {
    if (!$team_id) { die("Please create a team first!"); }
    $p_id = $_GET['add_to_draft'];
    
    // Check if already in draft
    $check = mysqli_query($conn, "SELECT * FROM draft_selections WHERE team_id = '$team_id' AND player_id = '$p_id'");
    if (mysqli_num_rows($check) == 0) {
        mysqli_query($conn, "INSERT INTO draft_selections (team_id, player_id) VALUES ('$team_id', '$p_id')");
    }
    header("Location: auction_list.php");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Auction Pool</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-4">
        <h2>Auction Pool</h2>
        <div class="row">
            <?php
            $res = mysqli_query($conn, "SELECT * FROM players WHERE status = 'Available'");
            while($p = mysqli_fetch_assoc($res)) {
                echo "
                <div class='col-md-3 mb-3'>
                    <div class='card shadow-sm'>
                        <div class='card-body text-center'>
                            <h5>{$p['name']}</h5>
                            <p class='text-muted'>Base: \${$p['base_price']}</p>
                            <a href='auction_list.php?add_to_draft={$p['id']}' class='btn btn-primary btn-sm'>Add to Draft</a>
                        </div>
                    </div>
                </div>";
            }
            ?>
        </div>
    </div>
</body>
</html>