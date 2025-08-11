<?php
include_once '../actions/create.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = isset($_POST['student_id']) ? trim($_POST['student_id']) : '';
    $new_password = isset($_POST['new_password']) ? $_POST['new_password'] : '';

    if (!empty($student_id) && !empty($new_password)) {
        // Check if account exists
        $stmt = $conn->prepare("SELECT * FROM accounts WHERE StudentID=?");
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            // Update password
            $update = $conn->prepare("UPDATE accounts SET password=? WHERE StudentID=?");
            $update->bind_param("si", $new_password, $student_id);
            if ($update->execute()) {
                $success = "Password reset successfully!";
            } else {
                $error = "Error resetting password.";
            }
            $update->close();
        } else {
            $error = "Account not found for this Student ID.";
        }
        $stmt->close();
    } else {
        $error = "Please fill in all fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <title>Reset Password | School Portal</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../bootstrap-5.3.7-dist/css/bootstrap.min.css">
    <style>
        body { background: #f8f9fa; }
        .reset-card {
            max-width: 400px;
            margin: 60px auto;
            border-radius: 18px;
            background: #fff;
            box-shadow: 0 2px 12px rgba(0,0,0,0.07);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="reset-card p-4 mt-5">
            <h3 class="text-center mb-4">Reset Password</h3>
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php elseif ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <form method="post" action="../actions/create.php" autocomplete="off">
                <div class="mb-3">
                    <label for="student_id" class="form-label">Student ID</label>
                    <input type="text" class="form-control" id="student_id" name="student_id" required>
                </div>
                <div class="mb-3">
                    <label for="new_password" class="form-label">New Password</label>
                    <input type="password" class="form-control" id="new_password" name="new_password" required>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary" name="reset_password">Reset Password</button>
                </div>
            </form>
            <div class="text-center mt-3">
                <a href="login.php" class="text-decoration-none">Back to Login</a>
            </div>
        </div>
    </div>