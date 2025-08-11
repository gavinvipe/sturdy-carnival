<?php
require_once "../actions/create.php";
require_once "../actions/edit.php";
require_once "../actions/delete.php";

// Redirect to login if not authenticated
if (!isset($_SESSION['admin_username'])) {
    header("Location: admin_login.php");
    exit();
}


// Fetch data for tables
include_once '../config/connect.php';
$students = $conn->query("SELECT s.StudentID, s.StudentName, d.DepartmentName, d.DepartmentID FROM Students s LEFT JOIN Departments d ON s.DepartmentID = d.DepartmentID");
$courses = $conn->query("SELECT * FROM Courses");
$departments = $conn->query("SELECT * FROM Departments");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | School Management Portal</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../bootstrap-5.3.7-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
  <!-- Sidebar -->
  <div class="sidebar" id="sidebar">
    <div class="sidebar-header text-center">
        <span>Admin Panel</span>
    </div>
    <ul class="nav flex-column mt-4">
        <li class="nav-item">
            <a class="nav-link active" href="#" data-section="dashboard" onclick="showSection('dashboard')">Dashboard</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#" data-section="students" onclick="showSection('students')">Students</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#" data-section="courses" onclick="showSection('courses')">Courses</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#" data-section="departments" onclick="showSection('departments')">Departments</a>
        </li>
        <li class="nav-item mt-4">
            <a class="nav-link text-danger" href="admin_login.php">Logout</a>
        </li>
    </ul>
  </div>
  <button class="sidebar-toggle" id="sidebarToggle" onclick="toggleSidebar()" title="Toggle Menu">
      <span id="sidebarToggleIcon">&#9776;</span>
  </button>

  <div class="main-content" id="mainContent">
    <nav class="navbar navbar-light bg-light shadow-sm mb-4">
        <div class="container-fluid text-body">
            <span class="navbar-brand mb-0 h1">School Management Admin Dashboard</span>
            <span class="navbar-text">Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
        </div>
    </nav>
    <div class="container-fluid">
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($_GET['success']); ?></div>
        <?php endif; ?>
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
        <?php endif; ?>

        <!-- Dashboard Cards -->
        <div class="row mb-4" id="section-dashboard">
            <div class="col-md-4 mb-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body text-center">
                      <img src="../images/student-male.png" alt="">
                        <h5 class="card-title">Students</h5>
                        <p class="card-text">Manage all students in the system.</p>
                        <button class="btn btn-primary" onclick="showSection('students')">View Table</button>
                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addStudentModal">Add New</button>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body text-center">
                      <img src="../images/book.png" alt="">
                        <h5 class="card-title">Courses</h5>
                        <p class="card-text">Manage all courses offered.</p>
                        <button class="btn btn-primary" onclick="showSection('courses')">View Table</button>
                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addCourseModal">Add New</button>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body text-center">
                      <img src="../images/department.png" alt="">
                        <h5 class="card-title">Departments</h5>
                        <p class="card-text">Manage all departments.</p>
                        <button class="btn btn-primary" onclick="showSection('departments')">View Table</button>
                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addDepartmentModal">Add New</button>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body text-center">
                       <img src="../images/registration.png" alt=""> 
                        <h5 class="card-title">Grading</h5>
                        <p class="card-text">Assign or update student grades for courses.</p>
                        <button class="btn btn-warning mb-2" data-bs-toggle="modal" data-bs-target="#gradeStudentModal">Grade Student</button>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body text-center">
                      <br><br>
                        <h5 class="card-title">Student Accounts</h5>
                        <p class="card-text">Manage student login accounts.</p>
                        <button class="btn btn-secondary mb-2" data-bs-toggle="modal" data-bs-target="#setStudentAccountModal">Set/Update Password</button>
                        <button class="btn btn-danger mb-2" data-bs-toggle="modal" data-bs-target="#deleteStudentAccountModal">Delete Account</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Students Section -->
        <div id="section-students" class="dashboard-section" style="display:none;">
            <button class="btn btn-secondary mb-3" onclick="showSection('dashboard')">← Back to Dashboard</button>
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h4>Students</h4>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addStudentModal">Add Student</button>
            </div>
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Department</th>
                        <th>Courses</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $studentModals = [];
                if ($students && $students->num_rows > 0): while($studentRow = $students->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $studentRow['StudentID']; ?></td>
                        <td><?php echo htmlspecialchars($studentRow['StudentName']); ?></td>
                        <td><?php echo htmlspecialchars($studentRow['DepartmentName']); ?></td>
                        <td>
                            <?php
                            // Fetch courses registered by this student
                            $studentId = $studentRow['StudentID'];
                            $regRes = $conn->query("SELECT c.CourseName FROM Registrations r JOIN Courses c ON r.CourseID = c.CourseID WHERE r.StudentID = '$studentId'");
                            if ($regRes === false) {
                                echo '<span class="text-danger">Error loading courses</span>';
                            } elseif ($regRes->num_rows > 0) {
                                $courseNames = [];
                                while ($regRow = $regRes->fetch_assoc()) {
                                    $courseNames[] = htmlspecialchars($regRow['CourseName']);
                                }
                                echo implode(', ', $courseNames);
                            } else {
                                echo '<span class="text-muted">None</span>';
                            }
                            ?>
                        </td>
                        <td class="table-actions">
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editStudentModal<?php echo $studentRow['StudentID']; ?>">Edit</button>
                            <form method="post" action="../actions/delete.php" style="display:inline;">
                                <input type="hidden" name="student_id" value="<?php echo $studentRow['StudentID']; ?>">
                                <button type="submit" class="btn btn-danger btn-sm" name="delete_student" onclick="return confirm('Delete this student?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php
                // Collect modal HTML for each student
                $modalHtml = '
                <div class="modal fade" id="editStudentModal'.$studentRow['StudentID'].'" tabindex="-1" aria-labelledby="editStudentModalLabel'.$studentRow['StudentID'].'" aria-hidden="true">
                  <div class="modal-dialog">
                    <form method="post" action="../actions/edit.php" class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="editStudentModalLabel'.$studentRow['StudentID'].'">Edit Student</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                        <input type="hidden" name="student_id" value="'.$studentRow['StudentID'].'">
                        <div class="mb-3">
                          <label class="form-label">Name</label>
                          <input type="text" class="form-control" name="student_name" value="'.htmlspecialchars($studentRow['StudentName']).'" required>
                        </div>
                        <div class="mb-3">
                          <label class="form-label">Department</label>
                          <select class="form-select" name="student_department" required>
                            <option value="">Select Department</option>';
                            $deptRes2 = $conn->query("SELECT DepartmentID, DepartmentName FROM Departments");
                            while($dept2 = $deptRes2->fetch_assoc()) {
                                $selected = ($studentRow['DepartmentID'] == $dept2['DepartmentID']) ? 'selected' : '';
                                $modalHtml .= '<option value="'.$dept2['DepartmentID'].'" '.$selected.'>'.htmlspecialchars($dept2['DepartmentName']).'</option>';
                            }
                $modalHtml .= '
                          </select>
                        </div>
                      </div>
                      <div class="modal-footer">
                        <button type="submit" name="edit_student" class="btn btn-primary">Save Changes</button>
                      </div>
                    </form>
                  </div>
                </div>';
                $studentModals[] = $modalHtml;
                endwhile; else: ?>
                    <tr><td colspan="4" class="text-center">No students found.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php
        // Output all edit modals after the table
        if (!empty($studentModals)) {
            foreach ($studentModals as $modalHtml) {
                echo $modalHtml;
            }
        }
        ?>

        <!-- Courses Section -->
        <div id="section-courses" class="dashboard-section" style="display:none;">
            <button class="btn btn-secondary mb-3" onclick="showSection('dashboard')">← Back to Dashboard</button>
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h4>Courses</h4>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addCourseModal">Add Course</button>
            </div>
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $courses->data_seek(0); // Reset pointer if reused
                if ($courses && $courses->num_rows > 0): while($courseRow = $courses->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $courseRow['CourseID']; ?></td>
                        <td><?php echo htmlspecialchars($courseRow['CourseName']); ?></td>
                        <td class="table-actions">
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editCourseModal<?php echo $courseRow['CourseID']; ?>">Edit</button>
                            <form method="post" action="../actions/delete.php" style="display:inline;">
                              <input type="hidden" name="course_id" value="<?php echo $courseRow['CourseID']; ?>">
                              <button type="submit" class="btn btn-danger btn-sm" name="delete_course" onclick="return confirm('Delete this course?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; else: ?>
                    <tr><td colspan="3" class="text-center">No courses found.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Departments Section -->
        <div id="section-departments" class="dashboard-section" style="display:none;">
            <button class="btn btn-secondary mb-3" onclick="showSection('dashboard')">← Back to Dashboard</button>
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h4>Departments</h4>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addDepartmentModal">Add Department</button>
            </div>
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Head</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $departments->data_seek(0); // Reset pointer if reused
                if ($departments && $departments->num_rows > 0): while($deptRow = $departments->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $deptRow['DepartmentID']; ?></td>
                        <td><?php echo htmlspecialchars($deptRow['DepartmentName']); ?></td>
                        <td><?php echo htmlspecialchars($deptRow['Head']); ?></td>
                        <td class="table-actions">
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editDepartmentModal<?php echo $deptRow['DepartmentID']; ?>">Edit</button>
                            <form method="post" action="../actions/delete.php" style="display:inline;">
                              <input type="hidden" name="department_id" value="<?php echo $deptRow['DepartmentID']; ?>">
                              <button type="submit" class="btn btn-danger btn-sm" name="delete_department" onclick="return confirm('Delete this department?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; else: ?>
                    <tr><td colspan="4" class="text-center">No departments found.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
  </div>

<!-- Add Student Modal -->
<div class="modal fade" id="addStudentModal" tabindex="-1" aria-labelledby="addStudentModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="post" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addStudentModalLabel">Add Student</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Student ID</label>
          <input type="text" class="form-control" name="student_id" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Name</label>
          <input type="text" class="form-control" name="student_name" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Department</label>
          <select class="form-select" name="department_id" required>
            <option value="">Select Department</option>
            <?php
            $deptRes = $conn->query("SELECT DepartmentID, DepartmentName FROM Departments");
            while($dept = $deptRes->fetch_assoc()):
            ?>
              <option value="<?php echo $dept['DepartmentID']; ?>"><?php echo htmlspecialchars($dept['DepartmentName']); ?></option>
            <?php endwhile; ?>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" name="add_student" class="btn btn-primary">Add Student</button>
      </div>
    </form>
  </div>
</div>

<!-- Add Course Modal -->
<div class="modal fade" id="addCourseModal" tabindex="-1" aria-labelledby="addCourseModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="post" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addCourseModalLabel">Add Course</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Course ID</label>
          <input type="text" class="form-control" name="course_id" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Course Name</label>
          <input type="text" class="form-control" name="course_name" required>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" name="add_course" class="btn btn-primary">Add Course</button>
      </div>
    </form>
  </div>
</div>

<!-- Add Department Modal -->
<div class="modal fade" id="addDepartmentModal" tabindex="-1" aria-labelledby="addDepartmentModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="post" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addDepartmentModalLabel">Add Department</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Department ID</label>
          <input type="text" class="form-control" name="department_id" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Department Name</label>
          <input type="text" class="form-control" name="department_name" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Head</label>
          <input type="text" class="form-control" name="head" required>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" name="add_department" class="btn btn-primary">Add Department</button>
      </div>
    </form>
  </div>
</div>

<!-- Grade Student Modal -->
<div class="modal fade" id="gradeStudentModal" tabindex="-1" aria-labelledby="gradeStudentModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="post" action="../actions/create.php" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="gradeStudentModalLabel">Grade Student</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Student</label>
          <select class="form-select" name="grade_student_id" required>
            <option value="">Select Student</option>
            <?php
            $studentsList = $conn->query("SELECT StudentID, StudentName FROM Students");
            while($s = $studentsList->fetch_assoc()):
            ?>
              <option value="<?php echo $s['StudentID']; ?>"><?php echo htmlspecialchars($s['StudentName']); ?></option>
            <?php endwhile; ?>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Course</label>
          <select class="form-select" name="grade_course_id" required>
            <option value="">Select Course</option>
            <?php
            $coursesList = $conn->query("SELECT CourseID, CourseName FROM Courses");
            while($c = $coursesList->fetch_assoc()):
            ?>
              <option value="<?php echo $c['CourseID']; ?>"><?php echo htmlspecialchars($c['CourseName']); ?></option>
            <?php endwhile; ?>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Grade</label>
          <input type="text" class="form-control" name="grade_value" required>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" name="grade_student" class="btn btn-primary">Save Grade</button>
      </div>
    </form>
  </div>
</div>

<!-- Set Student Account Modal -->
<div class="modal fade" id="setStudentAccountModal" tabindex="-1" aria-labelledby="setStudentAccountModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="post" action="../actions/create.php" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="setStudentAccountModalLabel">Set Student Account Password</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Student</label>
          <select class="form-select" name="acc_student_id" required>
            <option value="">Select Student</option>
            <?php
            $studentsList2 = $conn->query("SELECT StudentID, StudentName FROM Students");
            while($s = $studentsList2->fetch_assoc()):
            ?>
              <option value="<?php echo $s['StudentID']; ?>"><?php echo htmlspecialchars($s['StudentName']); ?></option>
            <?php endwhile; ?>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Password</label>
          <input type="password" class="form-control" name="acc_password" required>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" name="set_student_password" class="btn btn-primary">Set/Update Password</button>
      </div>
    </form>
  </div>
</div>

<!-- Delete Student Account Modal -->
<div class="modal fade" id="deleteStudentAccountModal" tabindex="-1" aria-labelledby="deleteStudentAccountModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="post" action="../actions/delete.php" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteStudentAccountModalLabel">Delete Student Account</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Student</label>
          <select class="form-select" name="del_acc_student_id" required>
            <option value="">Select Student</option>
            <?php
            $studentList = $conn->query("SELECT s.StudentID, s.StudentName FROM Students s INNER JOIN accounts a ON s.StudentID = a.StudentID");
            if ($studentList) {
                while($s = $studentList->fetch_assoc()) {
            ?>
              <option value="<?php echo $s['StudentID']; ?>"><?php echo htmlspecialchars($s['StudentName']); ?></option>
            <?php
                }
            }
            ?>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" name="delete_student_account" class="btn btn-danger">Delete Account</button>
      </div>
    </form>
  </div>
</div>

<?php
// --- Edit Course Modals ---
$courseModals = [];
$courses = $conn->query("SELECT * FROM Courses"); // Re-query to reset pointer
if ($courses && $courses->num_rows > 0) {
    while($courseRow = $courses->fetch_assoc()) {
        $courseModals[] = '
        <div class="modal fade" id="editCourseModal'.$courseRow['CourseID'].'" tabindex="-1" aria-labelledby="editCourseModalLabel'.$courseRow['CourseID'].'" aria-hidden="true">
          <div class="modal-dialog">
            <form method="post" action="../actions/edit.php" class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="editCourseModalLabel'.$courseRow['CourseID'].'">Edit Course</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <input type="hidden" name="course_id" value="'.$courseRow['CourseID'].'">
                <div class="mb-3">
                  <label class="form-label">Course Name</label>
                  <input type="text" class="form-control" name="course_name" value="'.htmlspecialchars($courseRow['CourseName']).'" required>
                </div>
              </div>
              <div class="modal-footer">
                <button type="submit" name="edit_course" class="btn btn-primary">Save Changes</button>
              </div>
            </form>
          </div>
        </div>';
    }
}
foreach ($courseModals as $modalHtml) echo $modalHtml;

// --- Edit Department Modals ---
$departmentModals = [];
$departments = $conn->query("SELECT * FROM Departments"); // Re-query to reset pointer
if ($departments && $departments->num_rows > 0) {
    while($deptRow = $departments->fetch_assoc()) {
        $departmentModals[] = '
        <div class="modal fade" id="editDepartmentModal'.$deptRow['DepartmentID'].'" tabindex="-1" aria-labelledby="editDepartmentModalLabel'.$deptRow['DepartmentID'].'" aria-hidden="true">
          <div class="modal-dialog">
            <form method="post" action="../actions/edit.php" class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="editDepartmentModalLabel'.$deptRow['DepartmentID'].'">Edit Department</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <input type="hidden" name="department_id" value="'.$deptRow['DepartmentID'].'">
                <div class="mb-3">
                  <label class="form-label">Department Name</label>
                  <input type="text" class="form-control" name="department_name" value="'.htmlspecialchars($deptRow['DepartmentName']).'" required>
                </div>
                <div class="mb-3">
                  <label class="form-label">Head</label>
                  <input type="text" class="form-control" name="head" value="'.htmlspecialchars($deptRow['Head']).'" required>
                </div>
              </div>
              <div class="modal-footer">
                <button type="submit" name="edit_department" class="btn btn-primary">Save Changes</button>
              </div>
            </form>
          </div>
        </div>';
    }
}
foreach ($departmentModals as $modalHtml) echo $modalHtml;
?>

<script src="../bootstrap-5.3.7-dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Sidebar toggle
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        sidebar.classList.toggle('closed');
        mainContent.classList.toggle('closed');
    }

    function getSectionFromUrl() {
    const params = new URLSearchParams(window.location.search);
    return params.get('section') || 'dashboard';
}

    // Section navigation
    function showSection(section) {
    ['dashboard', 'students', 'courses', 'departments'].forEach(function(sec) {
        var el = document.getElementById('section-' + sec);
        if (el) el.style.display = 'none';
    });
    var showEl = document.getElementById('section-' + section);
    if (showEl) {
        showEl.style.display = (section === 'dashboard') ? 'flex' : 'block';
    }
    document.querySelectorAll('.sidebar .nav-link').forEach(function(link) {
        link.classList.remove('active');
        if (link.getAttribute('data-section') === section) {
            link.classList.add('active');
        }
    });
}
    document.addEventListener('DOMContentLoaded', function() {
    showSection(getSectionFromUrl());
});
</script>
</body>
</html>