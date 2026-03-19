<?php
session_start();
include("config.php");
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'student'){
    header("Location: login.php"); exit();
}
$student_id = $_SESSION['user_id'];

$student_query = $conn->query("
SELECT users.fullname, users.email, students.admission_no, students.date_of_birth, students.gender
FROM students
JOIN users ON students.user_id = users.id
WHERE students.user_id = $student_id
");

$student = $student_query->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body style="background:#f4f6f9;">
<div class="container-fluid">
<div class="row">
 <!-- Sidebar -->
        <div class="col-md-2 sidebar">
              <div class="logo">
            <img src="logo.jpg" alt="Logo">
            
        </div>
            <h4 class="text-center mb-4">
                <i class="fa-solid fa-user-graduate"></i> Student
            </h4>
          

            <a href="student_dashboard.php"><i class="fa-solid fa-gauge"></i> Dashboard</a>
            <a href="student_subjects.php"><i class="fa-solid fa-book"></i> My Subjects</a>
            <a href="student_results.php"><i class="fa-solid fa-chart-line"></i> Results</a>
            <a href="student_grades.php"><i class="fa-solid fa-chart-line"></i>grades</a>
            <a href="student_notes.php"><i class="fa-solid fa-chart-line"></i> notes</a>
            <a href="student_assignments.php"><i class="fa-solid fa-chart-line"></i> Assignments</a>
            <a href="student_profile.php"><i class="fa-solid fa-user"></i> Profile</a>
            <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </div>
<div class="col-md-10 p-4">
<h3>My Profile</h3>
<div class="card p-4 mt-3" style="border-radius:15px; box-shadow:0 4px 10px rgba(0,0,0,0.05);">
    <p><strong>Full Name:</strong> <?php echo htmlspecialchars($student['fullname']); ?></p>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($student['email']); ?></p>
    
    <p><strong>Admission No:</strong> <?php echo htmlspecialchars($student['admission_no']); ?></p>
</div>
</div>
</div>
</div>
</body>
</html>