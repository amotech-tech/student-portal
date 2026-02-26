<?php
session_start();
include "config.php";

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'student'){
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['user_id'];

/* Fetch student info */
$student = $conn->query("SELECT * FROM users WHERE id='$student_id'")->fetch_assoc();

/* Count classes enrolled */
$classCount = $conn->query("SELECT COUNT(*) as total FROM classes WHERE id=".$student['id'])->fetch_assoc()['total'];

/* Count subjects grades */
$gradesCount = $conn->query("SELECT COUNT(*) as total FROM grades WHERE student_id='$student_id'")->fetch_assoc()['total'];

/* Attendance percentage */
$totalAttendance = $conn->query("SELECT COUNT(*) as total FROM attendance WHERE student_id='$student_id'")->fetch_assoc()['total'];
$presentCount = $conn->query("SELECT COUNT(*) as total FROM attendance WHERE student_id='$student_id' AND status='Present'")->fetch_assoc()['total'];
$attendancePercent = $totalAttendance>0 ? round(($presentCount/$totalAttendance)*100,2) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Student Dashboard</title>
<style>
* {margin:0;padding:0;box-sizing:border-box;}
body {font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; display:flex; background:#ECECEC;}
.sidebar {width:220px; background:#492828; min-height:100vh; display:flex; flex-direction:column; align-items:center; padding-top:20px; color:white;}
.sidebar img {width:80px; height:80px; border-radius:50%; object-fit:cover; margin-bottom:10px; border:2px solid #84934A;}
.sidebar h2 {margin-bottom:20px; font-size:18px;}
.sidebar a {width:100%; padding:14px 20px; text-decoration:none; color:white; display:block; border-top:1px solid rgba(255,255,255,0.1);}
.sidebar a:hover {background:#656D3F;}
.main {flex:1; padding:25px;}
.main h1 {color:#492828; margin-bottom:20px;}
.cards {display:flex; gap:20px; flex-wrap:wrap;}
.card {flex:1 1 280px; background:white; padding:22px; border-radius:12px; border-left:5px solid #84934A; box-shadow:0 2px 6px rgba(0,0,0,0.15); text-align:center;}
.card h3 {color:#492828; margin-bottom:10px; font-size:18px;}
.card p {font-size:28px; font-weight:bold; color:#656D3F;}
.btn {display:inline-block; padding:10px 18px; background:#84934A; color:white; border-radius:6px; text-decoration:none; margin-top:12px; font-size:15px;}
.btn:hover {background:#656D3F;}
@media(max-width:800px){.cards{flex-direction:column;}}
</style>
</head>
<body>

<div class="sidebar">
<img src="uploads/<?php echo $student['profile_picture']; ?>" alt="Profile">
<h2><?php echo $student['fullname']; ?></h2>
<a href="student_dashboard.php">Dashboard</a>
<a href="student_notes.php">Notes</a>
<a href="student_assignments.php">Assignments</a>
<a href="student_grades.php">Results</a>
<a href="student_profile.php">Profile</a>
<a href="logout.php">Logout</a>
</div>

<div class="main">
<h1>Student Dashboard</h1>

<div class="cards">
<div class="card"><h3>Class</h3><p><?php echo $classCount; ?></p></div>
<div class="card"><h3>Grades</h3><p><?php echo $gradesCount; ?></p></div>
<div class="card"><h3>Attendance</h3><p><?php echo $attendancePercent; ?>%</p></div>
<div class="card"><h3>Quick Actions</h3>
<a class="btn" href="student_notes.php">View Notes</a>
<a class="btn" href="student_assignments.php">Assignments</a>
</div>
</div>
</div>

</body>
</html>