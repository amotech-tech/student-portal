<?php
session_start();
include("config.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'student') {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['user_id'];
$subjects = $conn->query("
    SELECT s.id, s.name, s.code 
    FROM subjects s
    JOIN student_subjects ss ON s.id = ss.subject_id
    WHERE ss.student_id = $student_id
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Subjects</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { background:#f4f6f9; }
        .sidebar{ height:100vh; background:#1e293b; color:white; padding-top:20px; }
        .sidebar a{ color:#cbd5e1; text-decoration:none; display:block; padding:12px 20px; }
        .sidebar a:hover{ background:#334155; color:white; }
        .card{ border:none; border-radius:15px; box-shadow:0 4px 10px rgba(0,0,0,0.05); }
    </style>
</head>
<body>
<div class="container-fluid">
  <!-- Sidebar -->
        <div class="col-md-2 sidebar">
            <h4 class="text-center mb-4">
                <i class="fa-solid fa-user-graduate"></i> Student
            </h4>

            <a href="student_dashboard.php"><i class="fa-solid fa-gauge"></i> Dashboard</a>
            <a href="student_subjects.php"><i class="fa-solid fa-book"></i> My Subjects</a>
            <a href="student_results.php"><i class="fa-solid fa-chart-line"></i> Results</a>
            <a href="student_grades.php"><i class="fa-solid fa-chart-line"></i>grades</a>
            <a href="student_notes"><i class="fa-solid fa-chart-line"></i> notes</a>
            <a href="student_assignments.php"><i class="fa-solid fa-chart-line"></i> Assignments</a>
            <a href="student_profile.php"><i class="fa-solid fa-user"></i> Profile</a>
            <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </div>


        <div class="col-md-10 p-4">
            <h3>My Subjects</h3>
            <div class="row g-4 mt-3">
                <?php while($s = $subjects->fetch_assoc()) { ?>
                    <div class="col-md-4">
                        <div class="card p-3">
                            <h5><?php echo htmlspecialchars($s['name']); ?></h5>
                            <small>Code: <?php echo $s['code']; ?></small>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
</body>
</html>