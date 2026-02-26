<?php
session_start();
include "config.php";

if($_SESSION['role'] != 'student'){
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['user_id'];
$student = $conn->query("SELECT * FROM users WHERE id='$student_id'")->fetch_assoc();

/* Fetch grades */
$grades = $conn->query("SELECT g.*, c.name FROM grades g 
                        JOIN classes c ON g.id = c.id 
                        WHERE g.student_id='$student_id' ORDER BY g.id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Grades</title>
<style>
body{font-family:'Segoe UI'; 
background:#ECECEC;
 display:flex; margin:0;}
.sidebar{width:220px;
 background:#492828; 
 color:white; 
 display:flex; 
 flex-direction:column; 
 align-items:center; 
 padding-top:20px;}
.sidebar img{width:80px;
 height:80px; 
 border-radius:50%;
  object-fit:cover;
   margin-bottom:10px; 
   border:2px solid #84934A;}
.sidebar h2{margin-bottom:20px;
 font-size:18px;}
.sidebar a{width:100%; 
padding:14px 20px; 
text-decoration:none; 
color:white; 
display:block;
 border-top:1px solid rgba(255,255,255,0.1);}
.sidebar a:hover{background:#656D3F;}
.main{flex:1; padding:25px;}
h1{color:#492828; 
margin-bottom:20px;}
.table{width:100%;
 border-collapse:collapse; 
 background:white; 
 border-radius:10px; 
 overflow:hidden;
  box-shadow:0 2px 6px rgba(0,0,0,0.15);}
.table th, .table td{padding:12px 15px; 
text-align:left;}
.table th{background:#84934A;
 color:white;}
.table tr:nth-child(even){background:#f0f0f0;}
</style>
</head>
<body>

<div class="sidebar">
<img src="uploads/<?php echo $student['profile_picture']; ?>" alt="Profile">
<h2><?php echo $student['fullname']; ?></h2>
<a href="student_dashboard.php">Dashboard</a>
<a href="student_notes.php">Notes</a>
<a href="student_assignments.php">Assignments</a>
<a href="student_grades.php">Grades</a>
<a href="student_profile.php">Profile</a>
<a href="logout.php">Logout</a>
</div>

<div class="main">
<h1>My Grades</h1>

<?php if($grades->num_rows>0): ?>
<table class="table">
<tr>
<th>Class</th>
<th>Subject</th>
<th>Grade</th>
</tr>
<?php while($g = $grades->fetch_assoc()): ?>
<tr>
<td><?php echo $g['classname']; ?></td>
<td><?php echo $g['subject']; ?></td>
<td><?php echo $g['grade']; ?></td>
</tr>
<?php endwhile; ?>
</table>
<?php else: ?>
<p>No grades available yet.</p>
<?php endif; ?>

</div>
</body>
</html>