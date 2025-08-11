<?php
session_start();
include_once '../config/connect.php';

$login_error = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if (!empty($username) && !empty($password)) {
        // Replace 'admins' with your actual admin table name
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            $_SESSION['admin_username'] = $username;
            header('Location: admin_dashboard.php');
            exit();
        } else {
            $login_error = true;
        }
        $stmt->close();
    } else {
        $login_error = true;
    }
}
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <title>Admin Login | School Portal</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 5.3 -->
    <link rel="stylesheet" href="../bootstrap-5.3.7-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <div class="admin-login-card shadow p-5 mt-5">
            <h2 class="text-center admin-title mb-4">Admin Portal</h2>
            <p class="text-center mb-4">Sign in to manage the system</p>
            <?php if ($login_error): ?>
                <div class="alert alert-danger text-center">⚠️ Invalid username or password.</div>
            <?php endif; ?>
            <form method="post" autocomplete="off">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" required autofocus>
                    <?php
                        if (isset($_POST['username'])) {
                            $_SESSION['admin_username'] = htmlspecialchars($_POST['username']);
                        }
                    ?>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-primary btn-block">Login</button>
                </div>
                <div class="text-center">
                    <a href="../pages/reset.php" class="text-info">Forgot Password?</a>
                </div>
            </form>
        </div>
    </div>
    <script src="../bootstrap-5.3.7-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>