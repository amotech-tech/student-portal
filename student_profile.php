<?php
session_start();
include "config.php";

if($_SESSION['role'] != 'student'){
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['user_id'];
$student = $conn->query("SELECT * FROM users WHERE id='$student_id'")->fetch_assoc();

if(isset($_POST['update'])){
    $fullname = $_POST['fullname'];
    if(isset($_FILES['profile_picture']) && $_FILES['profile_picture']['name'] != ''){
        $filename = time().'_'.$_FILES['profile_picture']['name'];
        move_uploaded_file($_FILES['profile_picture']['tmp_name'], 'uploads/'.$filename);
        $conn->query("UPDATE users SET fullname='$fullname', profile_picture='$filename' WHERE id='$student_id'");
    } else {
        $conn->query("UPDATE users SET fullname='$fullname' WHERE id='$student_id'");
    }
    header("Location: student_profile.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Student Profile</title>
<style>
body{font-family:'Segoe UI'; background:#ECECEC; display:flex; margin:0;}
.sidebar{width:220px; background:#492828; color:white; display:flex; flex-direction:column; align-items:center; padding-top:20px;}
.sidebar img{width:80px; height:80px; border-radius:50%; object-fit:cover; margin-bottom:10px; border:2px solid #84934A;}
.sidebar h2{margin-bottom:20px; font-size:18px;}
.sidebar a{width:100%; padding:14px 20px; text-decoration:none; color:white; display:block; border-top:1px solid rgba(255,255,255,0.1);}
.sidebar a:hover{background:#656D3F;}
.main{flex:1; padding:25px;}
.profile-box{background:white; padding:22px; border-radius:12px; width:350px; margin:auto; box-shadow:0 2px 6px rgba(0,0,0,0.15); text-align:center;}
.profile-box img{width:120px; height:120px; border-radius:50%; object-fit:cover; margin-bottom:15px;}
.profile-box input[type="text"], .profile-box input[type="file"]{width:100%; padding:10px; margin:8px 0;}
.profile-box button{padding:10px 15px; background:#84934A; color:white; border:none; border-radius:5px; cursor:pointer;}
.profile-box button:hover{background:#656D3F;}
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
<div class="profile-box">
<h2>My Profile</h2>
<img src="uploads/<?php echo $student['profile_picture']; ?>" alt="Profile">
<form method="POST" enctype="multipart/form-data">
<input type="text" name="fullname" value="<?php echo $student['fullname']; ?>" required>
<input type="file" name="profile_picture">
<button type="submit" name="update">Update Profile</button>
</form>
</div>
</div>
</body>
</html>