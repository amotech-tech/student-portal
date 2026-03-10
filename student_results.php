<?php
session_start();
include("config.php");
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'student'){
    header("Location: login.php"); exit();
}
$student_id = $_SESSION['user_id'];
$results = $conn->query("SELECT * FROM grades WHERE student_id = $student_id ORDER BY year DESC, term DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Results</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body{ background:#f4f6f9; }
        .sidebar{ height:100vh; background:#1e293b; color:white; padding-top:20px; }
        .sidebar a{ color:#cbd5e1; text-decoration:none; display:block; padding:12px 20px; }
        .sidebar a:hover{ background:#334155; color:white; }
        .card{ border:none; border-radius:15px; box-shadow:0 4px 10px rgba(0,0,0,0.05); }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-2 sidebar">
            <h4 class="text-center mb-4"><i class="fa-solid fa-user-graduate"></i> Student</h4>
            <a href="student_dashboard.php"><i class="fa-solid fa-gauge"></i> Dashboard</a>
            <a href="student_subjects.php"><i class="fa-solid fa-book"></i> My Subjects</a>
            <a href="student_results.php"><i class="fa-solid fa-chart-line"></i> Results</a>
            <a href="student_assignments.php"><i class="fa-solid fa-list"></i> Assignments</a>
            <a href="student_profile.php"><i class="fa-solid fa-user"></i> Profile</a>
            <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </div>

        <div class="col-md-10 p-4">
            <h3>My Results</h3>
            <div class="card p-3 mt-3">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Subject ID</th>
                            <th>Grade</th>
                            <th>Term</th>
                            <th>Year</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($r = $results->fetch_assoc()){ ?>
                            <tr>
                                <td><?php echo $r['subject_id']; ?></td>
                                <td><?php echo $r['grade']; ?></td>
                                <td><?php echo $r['term']; ?></td>
                                <td><?php echo $r['year']; ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</body>
</html>