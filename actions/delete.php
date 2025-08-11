<?php
include_once "C:\\xampp\htdocs\DBMProject\config\connect.php";

// Delete Student
if (isset($_POST['delete_student'])) {
    $studentId = $_POST['student_id'];

    // First, delete all grades for this student
    $stmt = $conn->prepare("DELETE FROM Grading WHERE StudentID = ?");
    $stmt->bind_param("i", $studentId);
    $stmt->execute();
    $stmt->close();

    // Then, delete all registrations for this student
    $stmt = $conn->prepare("DELETE FROM Registrations WHERE StudentID = ?");
    $stmt->bind_param("i", $studentId);
    $stmt->execute();
    $stmt->close();

    // Then, delete the account for this student
    $stmt = $conn->prepare("DELETE FROM accounts WHERE StudentID = ?");
    $stmt->bind_param("i", $studentId);
    $stmt->execute();
    $stmt->close();

    // Now, delete the student
    $stmt = $conn->prepare("DELETE FROM Students WHERE StudentID = ?");
    $stmt->bind_param("i", $studentId);
    if ($stmt->execute()) {
        header("Location: ../admin/admin_dashboard.php?success=Student deleted!&section=students");
        exit();
    } else {
        header("Location: ../admin/admin_dashboard.php?error=" . urlencode($stmt->error));
        exit();
    }
    $stmt->close();
}

if (isset($_POST['delete_course'])) {
    $course_id = $_POST['course_id'];

    // First, delete all registrations for this course
    $stmt = $conn->prepare("DELETE FROM Registrations WHERE CourseID = ?");
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $stmt->close();

    // Now, delete the course
    $stmt = $conn->prepare("DELETE FROM Courses WHERE CourseID = ?");
    $stmt->bind_param("i", $course_id);
    if ($stmt->execute()) {
        header("Location: ../admin/admin_dashboard.php?success=Course deleted!&section=courses");
        exit();
    } else {
        header("Location: ../admin/admin_dashboard.php?error=Could not delete course.");
        exit();
    }
}

// Delete Department
if (isset($_POST['delete_department'])) {
    $department_id = $_POST['department_id'];

    // Check if any students are in this department
    $stmt = $conn->prepare("SELECT COUNT(*) FROM Students WHERE DepartmentID = ?");
    $stmt->bind_param("i", $department_id);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        header("Location: ../admin/admin_dashboard.php?error=Cannot delete department: students are assigned to it.");
        exit();
    }

    // Now, delete the department
    $stmt = $conn->prepare("DELETE FROM Departments WHERE DepartmentID = ?");
    $stmt->bind_param("i", $department_id);
    if ($stmt->execute()) {
        header("Location: ../admin/admin_dashboard.php?success=Department deleted!&section=departments");
        exit();
    } else {
        header("Location: ../admin/admin_dashboard.php?error=Could not delete department.");
        exit();
    }
}

// Delete Registration (student from a course)
if (isset($_POST['delete_registration'])) {
    $studentId = $_POST['student_id'];
    $courseId = $_POST['course_id'];
    $stmt = $conn->prepare("DELETE FROM Registrations WHERE StudentID = ? AND CourseID = ?");
    $stmt->bind_param("ii", $studentId, $courseId);
    if ($stmt->execute()) {
        header("Location: ../admin/admin_dashboard.php?success=Registration deleted!");
        exit();
    } else {
        header("Location: ../admin/admin_dashboard.php?error=" . urlencode($stmt->error));
        exit();
    }
    $stmt->close();
}

// Delete Grade
if (isset($_POST['delete_grade'])) {
    $studentId = $_POST['student_id'];
    $courseId = $_POST['course_id'];
    $stmt = $conn->prepare("DELETE FROM Grading WHERE StudentID = ? AND CourseID = ?");
    $stmt->bind_param("ii", $studentId, $courseId);
    if ($stmt->execute()) {
        header("Location: ../admin/admin_dashboard.php?success=Grade deleted!");
        exit();
    } else {
        header("Location: ../admin/admin_dashboard.php?error=" . urlencode($stmt->error));
        exit();
    }
    $stmt->close();
}

// Delete Student Account
if (isset($_POST['delete_student_account'])) {
    $studentId = $_POST['del_acc_student_id'];
    $stmt = $conn->prepare("DELETE FROM accounts WHERE StudentID = ?");
    $stmt->bind_param("i", $studentId);
    if ($stmt->execute()) {
        header("Location: ../admin/admin_dashboard.php?success=Student account deleted!");
        exit();
    } else {
        header("Location: ../admin/admin_dashboard.php?error=" . urlencode($stmt->error));
        exit();
    }
    $stmt->close();
}
?>