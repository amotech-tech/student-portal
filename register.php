<?php
session_start();
include "config.php";

function resizeImage($source, $destination, $new_width, $new_height) {
    list($width, $height, $type) = getimagesize($source);
    $image_p = imagecreatetruecolor($new_width, $new_height);

    switch ($type) {
        case IMAGETYPE_JPEG: $image = imagecreatefromjpeg($source); break;
        case IMAGETYPE_PNG:  $image = imagecreatefrompng($source); break;
        case IMAGETYPE_GIF:  $image = imagecreatefromgif($source); break;
        default: return false;
    }

    imagecopyresampled($image_p, $image, 0, 0, 0, 0,
        $new_width, $new_height, $width, $height);

    imagejpeg($image_p, $destination, 90);
    return true;
}

if(isset($_POST['register'])){

    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if($password !== $confirm){
        $error = "Passwords do not match!";
    } else {

        $check = $conn->prepare("SELECT id FROM users WHERE email=?");
        $check->bind_param("s",$email);
        $check->execute();
        $check->store_result();

        if($check->num_rows > 0){
            $error = "Email already registered!";
        } else {

            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $profile_picture = "default.png";

            if(!empty($_FILES['profile_picture']['name'])){

                $allowed = ['jpg','jpeg','png','gif'];
                $file_ext = strtolower(pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION));

                if(!in_array($file_ext,$allowed)){
                    $error = "Only JPG, PNG or GIF allowed.";
                } elseif($_FILES['profile_picture']['size'] > 2097152){
                    $error = "Image must be less than 2MB.";
                } else {

                    $upload_dir = "uploads/";
                    $new_name = time() . "." . $file_ext;
                    $target = $upload_dir . $new_name;

                    resizeImage($_FILES['profile_picture']['tmp_name'], $target, 200, 200);

                    $profile_picture = $new_name;
                }
            }

            if(!isset($error)){
                $stmt = $conn->prepare("INSERT INTO users (fullname,email,password,role,profile_picture) VALUES (?,?,?,?,?)");
                $stmt->bind_param("sssss",$fullname,$email,$hashed,$role,$profile_picture);
                $stmt->execute();

                $success = "Registration successful! You can now login.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register - School Portal</title>

<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Segoe UI',sans-serif;}
body{height:100vh;display:flex;}

.left{
    flex:1;
    background:linear-gradient(135deg,#492828,#656D3F);
    color:white;
    display:flex;
    flex-direction:column;
    justify-content:center;
    align-items:center;
    padding:40px;
    text-align:center;
}

.left h1{font-size:38px;margin-bottom:20px;}
.left p{max-width:350px;}

.right{
    flex:1;
    background:#ECECEC;
    display:flex;
    justify-content:center;
    align-items:center;
}

.register-box{
    width:400px;
    background:white;
    padding:40px;
    border-radius:12px;
    box-shadow:0 10px 25px rgba(0,0,0,0.1);
}

.logo{text-align:center;margin-bottom:20px;}
.logo img{
    width:80px;
    height:80px;
    border-radius:50%;
    object-fit:cover;
    margin-bottom:10px;
    border:3px solid #84934A;
}
.logo h2{color:#492828;}

.input-group{margin-bottom:15px;}
.input-group label{font-size:14px;font-weight:600;}
.input-group input, .input-group select{
    width:100%;
    padding:10px;
    margin-top:5px;
    border-radius:6px;
    border:1px solid #ccc;
}

button{
    width:100%;
    padding:12px;
    background:#84934A;
    border:none;
    border-radius:6px;
    color:white;
    font-size:16px;
    cursor:pointer;
    margin-top:10px;
}

button:hover{background:#656D3F;}

.error{color:red;text-align:center;margin-bottom:10px;}
.success{color:green;text-align:center;margin-bottom:10px;}

.login-link{text-align:center;margin-top:15px;}
.login-link a{color:#492828;text-decoration:none;font-weight:600;}

@media(max-width:768px){
    .left{display:none;}
}
</style>
</head>
<body>

<div class="left">
    <h1>Create Account</h1>
    <p>Register and upload your profile picture. 
    Image will automatically resize to fit the system.</p>
</div>

<div class="right">
    <div class="register-box">

        <div class="logo">
            <img id="preview" src="uploads/default.png">
            <h2>Register</h2>
        </div>

        <?php 
        if(isset($error)) echo "<div class='error'>$error</div>";
        if(isset($success)) echo "<div class='success'>$success</div>";
        ?>

        <form method="POST" enctype="multipart/form-data">

            <div class="input-group">
                <label>Full Name</label>
                <input type="text" name="fullname" required>
            </div>

            <div class="input-group">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>

            <div class="input-group">
                <label>Select Role</label>
                <select name="role" required>
                    <option value="">-- Select Role --</option>
                    <option value="admin">Admin</option>
                    <option value="teacher">Teacher</option>
                    <option value="student">Student</option>
                </select>
            </div>

            <div class="input-group">
                <label>Profile Picture</label>
                <input type="file" name="profile_picture" accept="image/*" onchange="previewImage(event)">
            </div>

            <div class="input-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>

            <div class="input-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" required>
            </div>

            <button type="submit" name="register">Register</button>

        </form>

        <div class="login-link">
            Already have an account? <a href="login.php">Login here</a>
        </div>

    </div>
</div>

<script>
function previewImage(event){
    var reader = new FileReader();
    reader.onload = function(){
        var output = document.getElementById('preview');
        output.src = reader.result;
    };
    reader.readAsDataURL(event.target.files[0]);
}
</script>

</body>
</html>