<?php
session_start();

include_once  '../config/connect.php';

$login_error = false;

// Get student ID and password from POST request
$student_id = isset($_POST['student']) ? $_POST['student'] : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

if (!empty($student_id) && !empty($password)) {
    // Prepare and execute query to check student credentials
    $stmt = $conn->prepare("SELECT * FROM accounts WHERE StudentID = ? AND Password = ?");
    $stmt->bind_param("is", $student_id, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        // Credentials are valid
        $_SESSION['student_id'] = $student_id;
        header('Location: ../home.php');
        exit();
    } else {
        // Invalid credentials
        $login_error = true; // just set the error, do not redirect
        // Invalid credentials
        $_SESSION['login_error'] = true;
    }
    $stmt->close();
} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Only show error if form was submitted
    $_SESSION['login_error'] = true;
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <title>Login | Student Portal</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Bootstrap 5.3 -->
    <link rel="stylesheet" href="..\bootstrap-5.3.7-dist\css\bootstrap.min.css">
    <link rel="stylesheet" href="..\css\style.css">
</head>
<body>

    <!-- Toggle Button -->
    <button type="button" class="btn btn-outline-secondary theme-toggle" onclick="toggleTheme()">🌓</button>

    <div class="container-fluid py-4">
        <div class="login-card p-4 shadow-bg">
            <h3 class="text-center mb-2">Student Portal</h3> 
            <h6 class="text-center mb-4">Sign In</h6>
            <?php if ($login_error) { ?>
                <div class="alert alert-danger">⚠️ Invalid student ID or password.</div>
            <?php } ?>

            <form method="post">

                <div class="mb-3">
                    <label for="student" class="form-label">Student ID</label>
                    <input type="text" class="form-control" id="student" name="student" required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <a href="reset.php" class="text-center mb-4"><p>Forgot Password</p></a>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Login</button>
                </div>

                <div class="mt-3 text-center">

                </div>
            </form>
        </div>
    </div>

    <script src="..\js\script.js"></script>
    <script src="..\bootstrap-5.3.7-dist\js\bootstrap.min.js"></script>
</body>
</html>
