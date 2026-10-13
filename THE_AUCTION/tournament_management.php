<?php
include 'db.php';
session_start();

// Add Tournament (Admin Only)
if (isset($_POST['add_tournament']) && $_SESSION['role'] == 'Admin') {
    $name = $_POST['t_name'];
    $status = $_POST['status'];
    mysqli_query($conn, "INSERT INTO tournaments (name, status) VALUES ('$name', '$status')");
}

$tournaments = mysqli_query($conn, "SELECT * FROM tournaments");
?>

<div class="container mt-4">
    <h2>Tournament Schedule</h2>
    
    <?php if ($_SESSION['role'] == 'Admin'): ?>
    <form method="POST" class="row g-3 mb-4">
        <div class="col-auto"><input type="text" name="t_name" class="form-control" placeholder="Tournament Name"></div>
        <div class="col-auto">
            <select name="status" class="form-control">
                <option>Upcoming</option>
                <option>Ongoing</option>
            </select>
        </div>
        <div class="col-auto"><button type="submit" name="add_tournament" class="btn btn-primary">Add</button></div>
    </form>
    <?php endif; ?>

    <table class="table table-striped table-bordered">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Tournament Name</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = mysqli_fetch_assoc($tournaments)): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['name']; ?></td>
                <td><span class="badge bg-info"><?php echo $row['status']; ?></span></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>