<?php
include("config.php");
session_start();

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm  = $_POST['confirm'];

    if ($password !== $confirm) {
        $message = "Passwords do not match!";
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $message = "Username already taken!";
        } else {
            $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $stmt->bind_param("ss", $username, $password);
            if ($stmt->execute()) {
                $message = "Account created successfully! You can now <a href='index.php'>login</a>.";
            } else {
                $message = "Error creating account.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Create Account</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
</head>
<body class="bg-light d-flex align-items-center justify-content-center vh-100">
    <div class="card shadow-lg" style="width: 400px;">
        <div class="card-body">
            <h3 class="card-title text-center mb-4">Create Account</h3>
            <form method="post">
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Confirm Password</label>
                    <input type="password" name="confirm" class="form-control" required>
                </div>
                <button class="btn btn-success w-100">Register</button>
            </form>
            <?php
            if (!empty($message)) {
                $isSuccess = str_contains($message, 'Account created successfully');
                $colorClass = $isSuccess ? 'text-success fw-bold' : 'text-danger';
                echo "<p class='mt-3 text-center $colorClass'>$message</p>";
            }
            ?>
            <hr>
            <p class="text-center mb-0">
                Already have an account? <a href="index.php">Login here</a>
            </p>
        </div>
    </div>
</body>
</html>