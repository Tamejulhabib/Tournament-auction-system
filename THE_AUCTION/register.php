<?php
include 'db.php';
$message = "";

if (isset($_POST['register'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $role     = $_POST['role'];

    // Check if username or email already exists
    $checkUser = mysqli_query($conn, "SELECT * FROM users WHERE username='$username' OR email='$email'");
    
    if (mysqli_num_rows($checkUser) > 0) {
        $message = "<div class='alert alert-danger'>Username or Email already exists!</div>";
    } else {
        // SQL Insert including Email
        $sql = "INSERT INTO users (username, email, password, role) VALUES ('$username', '$email', '$password', '$role')";
        
        if (mysqli_query($conn, $sql)) {
            $message = "<div class='alert alert-success'>Account created! <a href='login.php'>Login here</a></div>";
        } else {
            $message = "<div class='alert alert-danger'>Database Error: " . mysqli_error($conn) . "</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register - THE_AUCTION</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center" style="height: 100vh;">

<div class="container">
    <div class="card mx-auto shadow" style="max-width: 500px;">
        <div class="card-header bg-primary text-white text-center"><h4>User Registration</h4></div>
        <div class="card-body p-4">
            <?php echo $message; ?>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control" placeholder="name@example.com" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-select" required>
                        <option value="Admin">Admin</option>
                        <option value="Team Owner">Team Owner</option>
                        <option value="Player">Player</option>
                    </select>
                </div>
                <button type="submit" name="register" class="btn btn-primary w-100">Create Account</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>