<?php
session_start();
require_once "C:\\xampp\htdocs\DBMProject\config\connect.php";

$success = '';
$error = '';

// Add Student
if (isset($_POST['add_student'])) {
    $student_id = $_POST['student_id'];
    $student_name = $_POST['student_name'];
    $department_id = $_POST['department_id'];

    $stmt = $conn->prepare("INSERT INTO Students (StudentID, StudentName, DepartmentID) VALUES (?, ?, ?)");
    $stmt->bind_param("isi", $student_id, $student_name, $department_id);

    if ($stmt->execute()) {
        $success = "Student added!";
        header("Location: ../admin/admin_dashboard.php?success=Student added!");
        exit();
    } else {
        $error = "Error adding student: " . $stmt->error;
        header("Location: ../admin/admin_dashboard.php?error=$error");
        exit();
    }
    $stmt->close();
}

// Add Course
if (isset($_POST['add_course'])) {
    $course_id = $_POST['course_id'];
    $course_name = $_POST['course_name'];

    $stmt = $conn->prepare("INSERT INTO Courses (CourseID, CourseName) VALUES (?, ?)");
    $stmt->bind_param("is", $course_id, $course_name);

    if ($stmt->execute()) {
        $success = "Course added!";
        header("Location: ../admin/admin_dashboard.php?success=Course added!");
        exit();
    } else {
        $error = "Error adding course: " . $stmt->error;
        header("Location: ../admin/admin_dashboard.php?error=$error");
        exit();
    }
    $stmt->close();
}

// Add Department
if (isset($_POST['add_department'])) {
    $department_id = $_POST['department_id'];
    $department_name = $_POST['department_name'];
    $head = isset($_POST['head']) ? $_POST['head'] : '';

    $stmt = $conn->prepare("INSERT INTO Departments (DepartmentID, DepartmentName, Head) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $department_id, $department_name, $head);

    if ($stmt->execute()) {
        $success = "Department added!";
        header("Location: ../admin/admin_dashboard.php?success=Department added!");
        exit();
    } else {
        $error = "Error adding department: " . $stmt->error;
        header("Location: ../admin/admin_dashboard.php?error=$error");
        exit();
    }
    $stmt->close();
}

// Register Student to Course
if (isset($_POST['register_student_course'])) {
    $reg_student_id = $_POST['reg_student_id'];
    $reg_course_id = $_POST['reg_course_id'];

    $stmt = $conn->prepare("INSERT INTO Registrations (StudentID, CourseID) VALUES (?, ?)");
    $stmt->bind_param("ii", $reg_student_id, $reg_course_id);

    if ($stmt->execute()) {
        $success = "Student registered to course!";
        header("Location: ../admin/admin_dashboard.php?success=Student registered to course!");
        exit();
    } else {
        $error = "Error registering student to course: " . $stmt->error;
        header("Location: ../admin/admin_dashboard.php?error=$error");
        exit();
    }
    $stmt->close();
}

// Register multiple courses for a student (from student portal)
if (isset($_POST['register_courses'])) {
    if (!isset($_SESSION['student_id']) || !$_SESSION['student_id']) {
        header("Location: ../home.php?error=Not logged in.");
        exit();
    }
    $student_id = $_SESSION['student_id'];
    $selected_courses = isset($_POST['course_ids']) ? $_POST['course_ids'] : [];

    // Fetch current registrations
    $current = [];
    $res = $conn->query("SELECT CourseID FROM Registrations WHERE StudentID = $student_id");
    if ($res) {
        while ($r = $res->fetch_assoc()) {
            $current[] = $r['CourseID'];
        }
    }

    // Add new registrations
    foreach ($selected_courses as $cid) {
        if (!in_array($cid, $current)) {
            $stmt = $conn->prepare("INSERT INTO Registrations (StudentID, CourseID) VALUES (?, ?)");
            $stmt->bind_param("ii", $student_id, $cid);
            $stmt->execute();
            $stmt->close();
        }
    }

    // Remove unchecked registrations (unregister)
    foreach ($current as $cid) {
        if (!in_array($cid, $selected_courses)) {
            $stmt = $conn->prepare("DELETE FROM Registrations WHERE StudentID = ? AND CourseID = ?");
            $stmt->bind_param("ii", $student_id, $cid);
            $stmt->execute();
            $stmt->close();
        }
    }

    header("Location: ../home.php?success=Course registrations updated!");
    exit();
}

// Add/Update Student Account Password
if (isset($_POST['set_student_password'])) {
    $acc_student_id = $_POST['acc_student_id'];
    $acc_password = $_POST['acc_password'];

    // Check if account exists
    $check = $conn->prepare("SELECT * FROM accounts WHERE StudentID=?");
    $check->bind_param("i", $acc_student_id);
    $check->execute();
    $result = $check->get_result();

    if ($result && $result->num_rows > 0) {
        // Update password
        $stmt = $conn->prepare("UPDATE accounts SET password=? WHERE StudentID=?");
        $stmt->bind_param("si", $acc_password, $acc_student_id);

        if ($stmt->execute()) {
            header("Location: ../admin/admin_dashboard.php?success=Password updated!");
            exit();
        } else {
            $error = "Error updating password: " . $stmt->error;
            header("Location: ../admin/admin_dashboard.php?error=$error");
            exit();
        }
        $stmt->close();
    } else {
        // Insert new account
        $stmt = $conn->prepare("INSERT INTO accounts (StudentID, password) VALUES (?, ?)");
        $stmt->bind_param("is", $acc_student_id, $acc_password);

        if ($stmt->execute()) {
            header("Location: ../admin/admin_dashboard.php?success=Account created!");
            exit();
        } else {
            $error = "Error creating account: " . $stmt->error;
            header("Location: ../pages/admin_dashboard.php?error=$error");
            exit();
        }
        $stmt->close();
    }
    $check->close();
}

if (isset($_POST['reset_password'])){
    $acc_student_id = $_POST['student_id'];
    $acc_password = $_POST['new_password'];

    // Update password
    $stmt = $conn->prepare("UPDATE accounts SET password=? WHERE StudentID=?");
    $stmt->bind_param("si", $acc_password, $acc_student_id);
    if ($stmt->execute()) {
        header("Location: ../pages/login.php");
        exit();
    } else {
        $error = "Error updating password: " . $stmt->error;
        header("Location: ../pages/login.php?error=$error");
        exit();
    }
    $stmt->close();
}

// Grade Student for a Course
if (isset($_POST['grade_student'])) {
    $grade_student_id = $_POST['grade_student_id'];
    $grade_course_id = $_POST['grade_course_id'];
    $grade_value = $_POST['grade_value'];

    // Validate input
    if (empty($grade_student_id) || empty($grade_course_id) || $grade_value === null) {
        header("Location: ../pages/admin_dashboard.php?error=Missing grade data.");
        exit();
    }

    // Check if grade exists
    $check = $conn->prepare("SELECT * FROM Grading WHERE StudentID=? AND CourseID=?");
    $check->bind_param("ii", $grade_student_id, $grade_course_id);
    $check->execute();
    $result = $check->get_result();

    if ($result && $result->num_rows > 0) {
        // Update grade
        $stmt = $conn->prepare("UPDATE Grading SET Grade=? WHERE StudentID=? AND CourseID=?");
        $stmt->bind_param("sii", $grade_value, $grade_student_id, $grade_course_id);

        if ($stmt->execute()) {
            header("Location: ../pages/admin_dashboard.php?success=Grade updated!");
            exit();
        } else {
            $error = "Error updating grade: " . $stmt->error;
            header("Location: ../admin/admin_dashboard.php?error=$error");
            exit();
        }
        $stmt->close();
    } else {
        // Insert new grade
        $stmt = $conn->prepare("INSERT INTO Grading (StudentID, CourseID, Grade) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $grade_student_id, $grade_course_id, $grade_value);

        if ($stmt->execute()) {
            header("Location: ../admin/admin_dashboard.php?success=Grade assigned!");
            exit();
        } else {
            $error = "Error assigning grade: " . $stmt->error;
            header("Location: ../admin/admin_dashboard.php?error=$error");
            exit();
        }
        $stmt->close();
    }
    $check->close();
}

// Add/Edit Student Account Password
if (isset($_POST['set_student_password'])) {
    $acc_student_id = $_POST['acc_student_id'];
    $acc_password = $_POST['acc_password'];

    // Check if account exists
    $check = $conn->prepare("SELECT * FROM accounts WHERE StudentID=?");
    $check->bind_param("i", $acc_student_id);
    $check->execute();
    $result = $check->get_result();

    if ($result && $result->num_rows > 0) {
        // Update password
        $stmt = $conn->prepare("UPDATE accounts SET password=? WHERE StudentID=?");
        $stmt->bind_param("si", $acc_password, $acc_student_id);
        if ($stmt->execute()) {
            header("Location: ../admin/admin_dashboard.php?success=Password updated!");
            exit();
        } else {
            $error = "Error updating password: " . $stmt->error;
            header("Location: ../admin/admin_dashboard.php?error=$error");
            exit();
        }
        $stmt->close();
    } else {
        // Insert new account
        $stmt = $conn->prepare("INSERT INTO accounts (StudentID, password) VALUES (?, ?)");
        $stmt->bind_param("is", $acc_student_id, $acc_password);
        if ($stmt->execute()) {
            header("Location: ../admin/admin_dashboard.php?success=Account created!");
            exit();
        } else {
            $error = "Error creating account: " . $stmt->error;
            header("Location: ../admin/admin_dashboard.php?error=$error");
            exit();
        }
        $stmt->close();
    }
    $check->close();
}
?>