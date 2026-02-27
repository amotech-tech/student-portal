<?php
session_start();
include "config.php";

if(isset($_POST['login'])){

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, fullname, password, role, profile_picture FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0){

        $user = $result->fetch_assoc();

        if(password_verify($password, $user['password'])){

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['fullname'] = $user['fullname'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['profile_picture'] = $user['profile_picture'];

            if($user['role'] == 'admin'){
                header("Location: dashboard.php");
            }
            elseif($user['role'] == 'teacher'){
                header("Location: teacher_dashboard.php");
            }
            elseif($user['role'] == 'student'){
                header("Location: student_dashboard.php");
            }
            exit;

        } else {
            $error = "Invalid email or password.";
        }

    } else {
        $error = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login - School Portal</title>

<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Segoe UI',sans-serif;}

body{height:100vh;display:flex;}

/* LEFT SIDE */
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

.left p{
    font-size:16px;
    max-width:350px;
    margin-bottom:20px;
}

.left ul{
    text-align:left;
    font-size:14px;
    line-height:1.6;
}

/* RIGHT SIDE */
.right{
    flex:1;
    background:#ECECEC;
    display:flex;
    justify-content:center;
    align-items:center;
}

.login-box{
    width:360px;
    background:white;
    padding:40px;
    border-radius:12px;
    box-shadow:0 10px 25px rgba(0,0,0,0.1);
}

/* LOGO */
.logo{text-align:center;margin-bottom:20px;}
.logo img{width:60px;margin-bottom:10px;}
.logo h2{color:#492828;}

.input-group{margin-bottom:15px;}
.input-group label{font-size:14px;font-weight:600;}
.input-group input{
    width:100%;
    padding:10px;
    margin-top:5px;
    border-radius:6px;
    border:1px solid #ccc;
}

/* PASSWORD ICON */
.password-wrapper{position:relative;}
.password-wrapper span{
    position:absolute;
    right:10px;
    top:38px;
    cursor:pointer;
    font-size:14px;
    color:#656D3F;
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

.error{
    color:red;
    margin-bottom:10px;
    font-size:14px;
    text-align:center;
}

.register-link{
    margin-top:15px;
    text-align:center;
    font-size:14px;
}

.register-link a{
    color:#492828;
    text-decoration:none;
    font-weight:600;
}

.register-link a:hover{
    text-decoration:underline;
}

@media(max-width:768px){
    .left{display:none;}
}
</style>
</head>
<body>

<!-- LEFT SIDE -->
<div class="left">
    <h1>School Portal</h1>
    <p>Welcome ,Follow the steps below to access your dashboard:</p>

    <ul>
        <li>Use your registered email address</li>
        <li>Enter your password</li>
        <li>Click Login</li>
       
    </ul>
</div>

<!-- RIGHT SIDE -->
<div class="right">
    <div class="login-box">

        <div class="logo">
            <img src="logo.png" alt="Logo">
            <h2>Login</h2>
        </div>

        <?php if(isset($error)) echo "<div class='error'>$error</div>"; ?>

        <form method="POST">

            <div class="input-group">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>

            <div class="input-group password-wrapper">
                <label>Password</label>
                <input type="password" name="password" id="password" required>
                <span onclick="togglePassword()">Show</span>
            </div>

            <button type="submit" name="login">Login</button>

        </form>

        <div class="register-link">
            Don’t have an account? <a href="register.php">Register here</a>
        </div>

    </div>
</div>

<script>
function togglePassword(){
    var pass = document.getElementById("password");
    var text = event.target;

    if(pass.type === "password"){
        pass.type = "text";
        text.innerHTML = "Hide";
    } else {
        pass.type = "password";
        text.innerHTML = "Show";
    }
}
</script>

</body>
</html>