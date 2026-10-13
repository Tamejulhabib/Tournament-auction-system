<?php
include 'db.php';
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); }

$player_id = 1; // Example: This would normally come from a URL variable (?id=1)

// 1. Fetch Auction Data
$query = "SELECT a.*, p.name, p.base_price, p.image 
          FROM auctions a 
          JOIN players p ON a.player_id = p.id 
          WHERE a.player_id = $player_id";
$result = mysqli_query($conn, $query);
$auction = mysqli_fetch_assoc($result);

// 2. PHP Logic for Timer
$now = new DateTime();
$end_time = new DateTime($auction['end_time']);
$interval = $now->diff($end_time);
$seconds_left = $end_time->getTimestamp() - $now->getTimestamp();

// 3. Handle Bid Submission
if (isset($_POST['place_bid']) && $seconds_left > 0) {
    $bid_amount = $_POST['bid_amount'];
    $user_id = $_SESSION['user_id'];

    if ($bid_amount > $auction['current_highest_bid']) {
        $update = "UPDATE auctions SET current_highest_bid = $bid_amount, highest_bidder_id = $user_id WHERE player_id = $player_id";
        mysqli_query($conn, $update);
        header("Refresh:0"); // Refresh to show new bid
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Live Auction</title>
    <meta http-equiv="refresh" content="5">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card mx-auto" style="width: 25rem;">
        <img src="uploads/<?php echo $auction['image']; ?>" class="card-img-top" alt="Player">
        <div class="card-body text-center">
            <h5 class="card-title"><?php echo $auction['name']; ?></h5>
            <p class="text-danger fw-bold">
                Time Remaining: <?php echo ($seconds_left > 0) ? $interval->format('%i:%s') : "CLOSED"; ?>
            </p>
            <hr>
            <p>Current Bid: <strong>$<?php echo $auction['current_highest_bid']; ?></strong></p>
            
            <?php if ($seconds_left > 0 && $_SESSION['role'] == 'Team Owner'): ?>
                <form method="POST">
                    <input type="number" name="bid_amount" class="form-control mb-2" placeholder="Enter higher amount" required>
                    <button type="submit" name="place_bid" class="btn btn-success w-100">Place Bid</button>
                </form>
            <?php elseif ($seconds_left <= 0): ?>
                <div class="alert alert-dark">This Auction has Ended</div>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>