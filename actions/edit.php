<?php
require_once "C:\\xampp\htdocs\DBMProject\config\connect.php";

// Edit Student
if (isset($_POST['edit_student'])) {
    $studentId = $_POST['student_id'];
    $studentName = $_POST['student_name'];
    $studentDept = $_POST['student_department'];

    $stmt = $conn->prepare("UPDATE Students SET StudentName = ?, DepartmentID = ? WHERE StudentID = ?");
    $stmt->bind_param("sii", $studentName, $studentDept, $studentId);

    if ($stmt->execute()) {
        header("Location: ../admin/admin_dashboard.php?success=Student updated!&section=students");
        exit();
    } else {
        header("Location: ../admin/admin_dashboard.php?error=" . urlencode($stmt->error));
        exit();
    }
    $stmt->close();
}

if (isset($_POST['update_student'])) {
    $student_id = $_POST['student_id'];
    $student_name = $_POST['student_name'];

    $stmt = $conn->prepare("UPDATE Students SET StudentName = ? WHERE StudentID = ?");
    $stmt->bind_param("si", $student_name, $student_id);

    if ($stmt->execute()) {
        header("Location: home.php?success=Profile updated!");
        exit();
    } else {
        header("Location: home.php?error=Could not update profile.");
        exit();
    }
    $stmt->close();
}

// Edit Course
if (isset($_POST['edit_course'])) {
    $courseId = $_POST['course_id'];
    $courseName = $_POST['course_name'];

    $stmt = $conn->prepare("UPDATE Courses SET CourseName = ? WHERE CourseID = ?");
    $stmt->bind_param("si", $courseName, $courseId);

    if ($stmt->execute()) {
        header("Location: ../admin/admin_dashboard.php?success=Course updated!&section=courses");
        exit();
    } else {
        header("Location: ../admin/admin_dashboard.php?error=" . urlencode($stmt->error));
        exit();
    }
    $stmt->close();
}

// Edit Department
if (isset($_POST['edit_department'])) {
    $departmentId = $_POST['department_id'];
    $departmentName = $_POST['department_name'];
    $head = isset($_POST['head']) ? $_POST['head'] : '';

    $stmt = $conn->prepare("UPDATE Departments SET DepartmentName = ?, Head = ? WHERE DepartmentID = ?");
    $stmt->bind_param("ssi", $departmentName, $head, $departmentId);

    if ($stmt->execute()) {
        header("Location: ../admin/admin_dashboard.php?success=Department updated!&section=departments");
        exit();
    } else {
        header("Location: ../admin/admin_dashboard.php?error=" . urlencode($stmt->error));
        exit();
    }
    $stmt->close();
}

// Edit Student Grade
if (isset($_POST['edit_student_grade'])) {
    $studentId = $_POST['student_id'];
    $courseId = $_POST['course_id'];
    $grade = $_POST['grade'];

    // Check if grade exists
    $check = $conn->prepare("SELECT * FROM Grading WHERE StudentID=? AND CourseID=?");
    $check->bind_param("ii", $studentId, $courseId);
    $check->execute();
    $result = $check->get_result();

    if ($result && $result->num_rows > 0) {
        // Update grade
        $stmt = $conn->prepare("UPDATE Grading SET Grade = ? WHERE StudentID = ? AND CourseID = ?");
        $stmt->bind_param("sii", $grade, $studentId, $courseId);
    } else {
        // Insert new grade
        $stmt = $conn->prepare("INSERT INTO Grading (StudentID, CourseID, Grade) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $studentId, $courseId, $grade);
    }
    if ($stmt->execute()) {
        header("Location: ../admin/admin_dashboard.php?success=Grade updated!");
        exit();
    } else {
        header("Location: ../admin/admin_dashboard.php?error=" . urlencode($stmt->error));
        exit();
    }
    $stmt->close();
    $check->close();
}

// Edit Course Registration (change course for a student)
if (isset($_POST['edit_courseregistration'])) {
    $studentId = $_POST['student_id'];
    $oldCourseId = $_POST['old_course_id'];
    $newCourseId = $_POST['new_course_id'];

    $stmt = $conn->prepare("UPDATE Registrations SET CourseID = ? WHERE StudentID = ? AND CourseID = ?");
    $stmt->bind_param("iii", $newCourseId, $studentId, $oldCourseId);

    if ($stmt->execute()) {
        header("Location: ../admin/admin_dashboard.php?success=Course registration updated!");
        exit();
    } else {
        header("Location: ../admin/admin_dashboard.php?error=" . urlencode($stmt->error));
        exit();
    }
    $stmt->close();
}
?>