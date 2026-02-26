<?php
session_start();
include "config.php";

if($_SESSION['role'] != 'student'){
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['user_id'];
$student = $conn->query("SELECT * FROM users WHERE id='$student_id'")->fetch_assoc();

/* Fetch notes for the student's class */
$notes = $conn->query("SELECT * FROM class_notes WHERE id=".$student['id']." ORDER BY uploaded_on DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Class Notes</title>
<style>
    body{font-family:'Segoe UI'; 
     background:#ECECEC; 
     display:flex; 
     margin:0;}
    .sidebar{width:220px; 
    background:#492828; 
    color:white; display:flex; flex-direction:column; align-items:center; padding-top:20px;}
    .sidebar img{width:80px; height:80px; border-radius:50%; object-fit:cover; margin-bottom:10px; border:2px solid #84934A;}
    .sidebar h2{margin-bottom:20px; font-size:18px;}
    .sidebar a{width:100%; padding:14px 20px; text-decoration:none; color:white; display:block; border-top:1px solid rgba(255,255,255,0.1);}
    .sidebar a:hover{background:#656D3F;}
    .main{flex:1; padding:25px;}
    h1{color:#492828; margin-bottom:20px;}
    .note-card{background:white; padding:18px; border-radius:10px; margin-bottom:15px; border-left:5px solid #84934A; box-shadow:0 2px 6px rgba(0,0,0,0.15);}
    .note-card h3{color:#492828; margin-bottom:5px;}
    .note-card a{display:inline-block; margin-top:8px; padding:8px 14px; background:#84934A; color:white; border-radius:5px; text-decoration:none;}
    .note-card a:hover{background:#656D3F;}
</style>
</head>
<body>
<div class="sidebar">
<img src="uploads/<?php echo $student['profile_picture']; ?>" alt="Profile">
<h2><?php echo $student['fullname']; ?></h2>
<a href="student_dashboard.php">Dashboard</a>
<a href="student_notes.php">Notes</a>
<a href="student_assignments.php">Assignments</a>
<a href="student_profile.php">Profile</a>
<a href="logout.php">Logout</a>
</div>

<div class="main">
<h1>Class Notes</h1>

<?php if($notes->num_rows>0): ?>
<?php while($n = $notes->fetch_assoc()): ?>
<div class="note-card">
<h3><?php echo $n['title']; ?></h3>
<p>Uploaded: <?php echo date("d-M-Y", strtotime($n['uploaded_on'])); ?></p>
<a href="uploads/notes/<?php echo $n['file']; ?>" download>Download</a>
</div>
<?php endwhile; ?>
<?php else: ?>
<p>No notes uploaded yet.</p>
<?php endif; ?>
</div>
</body>
</html>