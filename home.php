<?php
require_once "actions/create.php";
require_once "actions/edit.php";
require_once "actions/delete.php";

if (!isset($_SESSION['student_id'])) {
    header("Location: pages/login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <title>Student Portal | School Management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
    <script src="js/script.js"></script>
</head>
<body class="bg-body text-body">

<?php
require_once 'actions/create.php';
require_once 'actions/edit.php';

$StudentID = isset($_SESSION['student_id']) ? intval($_SESSION['student_id']) : 0;
// Fetch student info
$studentInfo = $conn->query("SELECT s.StudentName, s.DepartmentID, d.DepartmentName FROM Students s LEFT JOIN Departments d ON s.DepartmentID = d.DepartmentID WHERE s.StudentID = $StudentID");
$student = $studentInfo ? $studentInfo->fetch_assoc() : null;
// Fetch courses for registration
$courses = $conn->query("SELECT CourseID, CourseName FROM Courses");
// Fetch registered courses for this student
$registeredCourses = $conn->query("SELECT c.CourseName, g.Grade FROM Registrations r LEFT JOIN Courses c ON r.CourseID = c.CourseID LEFT JOIN Grading g ON g.StudentID = r.StudentID AND g.CourseID = r.CourseID WHERE r.StudentID = $StudentID");
$registeredCourseIds = [];
$regRes = $conn->query("SELECT CourseID FROM Registrations WHERE StudentID = $StudentID");
if ($regRes) {
    while ($regRow = $regRes->fetch_assoc()) {
        $registeredCourseIds[] = $regRow['CourseID'];
    }
}
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
    <div class="container-fluid">
        <span class="navbar-brand">Student Portal</span>
        <div>
            <a href="pages/login.php" class="btn btn-outline-light btn-sm">Logout</a>
        </div>
    </div>
</nav>

<div class="container py-5">
  <div class="row justify-content-center mb-4">
    <div class="col-md-6">
      <div class="card shadow border-0 rounded-4">
        <div class="card-body text-center">
          <div class="d-flex flex-column align-items-center">
            <div class="bg-white rounded-circle shadow mb-3 p-2" style="width: 110px; height: 110px;">
              <img src="images/student-male.png" alt="Student" class="img-fluid rounded-circle" style="width: 90px; height: 90px; object-fit: cover;">
            </div>
            <h4 class="fw-bold mb-1">Name: <?php echo $student ? htmlspecialchars($student['StudentName']) : ''; ?></h4>
            <div class="text-muted mb-2">Department: <?php echo $student ? htmlspecialchars($student['DepartmentName']) : ''; ?></div>
            <span class="badge bg-primary-subtle text-primary-emphasis px-3 py-2 fs-6 shadow-sm mb-3">
              ID: <?php echo htmlspecialchars($StudentID); ?>
            </span>
            <button class="btn btn-outline-primary px-4" data-bs-toggle="modal" data-bs-target="#editProfileModal">
              Edit Profile
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row row-cols-1 row-cols-md-2 g-4">
    <div class="col">
      <div class="card h-100 shadow-sm border-0">
        <div class="card-body text-center">
          <img src="images\registration.png" alt="">
          <h5 class="card-title mb-2">Course Registration</h5>
          <p class="text-muted small mb-3">Register for available courses.</p>
          <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#registrationModal">Register Courses</button>
        </div>
      </div>
    </div>
    <div class="col">
      <div class="card h-100 shadow-sm border-0">
        <div class="card-body text-center">
          <img src="images\book.png" alt="">
          <h5 class="card-title mb-2">My Courses & Grades</h5>
          <p class="text-muted small mb-3">View your registered courses and grades.</p>
          <button class="btn btn-success w-100" data-bs-toggle="modal" data-bs-target="#myCoursesModal">View Courses</button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Edit Profile Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="post" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editProfileModalLabel">Edit Profile</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label for="student_id" class="form-label">Student ID</label>
          <input type="text" class="form-control" id="student_id" name="student_id" value="<?php echo htmlspecialchars($StudentID); ?>" readonly>
        </div>
        <div class="mb-3">
          <label for="student_name" class="form-label">Name</label>
          <input type="text" class="form-control" id="student_name" name="student_name" value="<?php echo $student ? htmlspecialchars($student['StudentName']) : ''; ?>" required>
        </div>
        <div class="mb-3">
          <label for="student_department" class="form-label">Department</label>
          <input type="text" class="form-control" id="student_department" name="student_department" value="<?php echo $student ? htmlspecialchars($student['DepartmentName']) : ''; ?>" readonly>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" name="update_student" class="btn btn-primary">Save Changes</button>
      </div>
    </form>
  </div>
</div>

<!-- Registration Modal -->
<div class="modal fade" id="registrationModal" tabindex="-1" aria-labelledby="registrationModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="post" action="actions/create.php" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="registrationModalLabel">Register Courses</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Available Courses</label>
          <?php if ($courses) { while($row = $courses->fetch_assoc()): ?>
            <div class="form-check mb-2">
              <input class="form-check-input" type="checkbox" name="course_ids[]" value="<?php echo $row['CourseID']; ?>" id="course_<?php echo $row['CourseID']; ?>" <?php echo in_array($row['CourseID'], $registeredCourseIds) ? 'checked' : ''; ?>>
              <label class="form-check-label" for="course_<?php echo $row['CourseID']; ?>">
                <?php echo htmlspecialchars($row['CourseName']); ?>
              </label>
            </div>
          <?php endwhile; } ?>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" name="register_courses" class="btn btn-primary">Save Changes</button>
      </div>
    </form>
  </div>
</div>

<!-- My Courses Modal -->
<div class="modal fade" id="myCoursesModal" tabindex="-1" aria-labelledby="myCoursesModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="myCoursesModalLabel">My Courses & Grades</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <?php if ($registeredCourses && $registeredCourses->num_rows > 0): ?>
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>Course</th>
                <th>Grade</th>
              </tr>
            </thead>
            <tbody>
              <?php while($row = $registeredCourses->fetch_assoc()): ?>
                <tr>
                  <td><?php echo htmlspecialchars($row['CourseName']); ?></td>
                  <td><?php echo htmlspecialchars($row['Grade'] ?? 'N/A'); ?></td>
                </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        <?php else: ?>
          <div class="alert alert-info text-center">You have not registered for any courses yet.</div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

</body>

</html>
