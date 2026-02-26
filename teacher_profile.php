<?php
session_start();
include "config.php";

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'teacher'){
    header("Location: login.php");
    exit();
}

$teacher_id = $_SESSION['user_id'];

/* Fetch teacher info */
$teacher = $conn->query("SELECT * FROM users WHERE id='$teacher_id'")->fetch_assoc();

/* Handle profile update */
if(isset($_POST['update'])){

    $fullname = $_POST['fullname'];

    // Handle image upload
    if(isset($_FILES['profile_picture']) && $_FILES['profile_picture']['name'] != ''){

        $file_name = time().'_'.$_FILES['profile_picture']['name'];
        $target = 'uploads/'.$file_name;

        if(move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target)){
            $profile_pic = $file_name;
            $conn->query("UPDATE users SET fullname='$fullname', profile_picture='$profile_pic' WHERE id='$teacher_id'");
        } else {
            echo "<p style='color:red;'>Failed to upload image</p>";
        }

    } else {
        $conn->query("UPDATE users SET fullname='$fullname' WHERE id='$teacher_id'");
    }

    header("Location: teacher_profile.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Teacher Profile</title>
    <style>
        body { font-family: Arial; background:#f4f6f9; padding:20px; }
        .profile-box { background:white; padding:20px; border-radius:10px; width:350px; margin:auto; box-shadow:0 4px 8px rgba(0,0,0,0.1); text-align:center; }
        .profile-box img { width:120px; height:120px; border-radius:50%; object-fit:cover; margin-bottom:15px; }
        .profile-box input[type="text"], .profile-box input[type="file"] { width:100%; padding:10px; margin:8px 0; }
        .profile-box button { padding:10px 15px; background:#3498db; color:white; border:none; border-radius:5px; cursor:pointer; }
        .profile-box button:hover { background:#2980b9; }
    </style>
</head>
<body>

<div class="profile-box">
    <h2>My Profile</h2>
    <img src="uploads/<?php echo $teacher['profile_picture']; ?>" alt="Profile Picture">
    
    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="fullname" value="<?php echo $teacher['fullname']; ?>" required>
        <input type="file" name="profile_picture">
        <button type="submit" name="update">Update Profile</button>
    </form>

    <br>
    <a href="teacher_dashboard.php">Back to Dashboard</a>
</div>

</body>
</html>