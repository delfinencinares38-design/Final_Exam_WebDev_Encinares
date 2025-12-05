<?php
include("config.php");
session_start();

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, username FROM users WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        session_regenerate_id(true);
        $_SESSION['username'] = $row['username'];
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid login!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>CCDI Visitor Log - Login</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <script>
        function togglePassword() {
            const pw = document.getElementById("password");
            pw.type = pw.type === "password" ? "text" : "password";
        }
        function clearForm() {
            document.querySelector("form").reset();
        }
    </script>
</head>
<body class="bg-light d-flex align-items-center justify-content-center vh-100">
    <div class="card shadow-lg" style="width: 400px;">
        <div class="card-body text-center">
            <!-- Logo -->
            <img src="assets/img/ccdi_logo.jpg" alt="CCDI Logo" class="mb-3" style="max-width: 120px; height: auto;">
            
            <!-- Title -->
            <h3 class="card-title mb-4">CCDI Visitor Log System</h3>
            
            <!-- Login Form -->
            <form method="post">
                <div class="mb-3 text-start">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="mb-3 text-start">
                    <label class="form-label">Password</label>
                    <div class="input-group">
                        <input type="password" name="password" id="password" class="form-control" required>
                        <button type="button" class="btn btn-outline-secondary" onclick="togglePassword()">Show</button>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-primary w-100">Login</button>
                    <button type="button" class="btn btn-outline-danger w-100" onclick="clearForm()">Clear</button>
                </div>
                <?php if(!empty($error)) echo "<p class='text-danger mt-2'>$error</p>"; ?>

                <p class="text-center mt-3 mb-0">
                    Don’t have an account? <a href="create_account.php">Create Account</a>
                </p>
            </form>
            
            <hr>
            <p class="text-center mb-0">Administrator Login :</p>
            <p class="text-center mb-0">admin</p>
            <p class="text-center mb-0">admin123</p>
        </div>
    </div>
</body>
</html>