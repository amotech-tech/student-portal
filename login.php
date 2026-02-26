<?php
session_start(); // start session
include "config.php"; // database connection

if(isset($_POST['login'])){
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if user exists
    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($sql);

    if($result->num_rows == 1){
        $user = $result->fetch_assoc();

        // Verify password
        if(password_verify($password, $user['password'])){
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['fullname'] = $user['fullname'];
            $_SESSION['role'] = $user['role'];

           if($user['role']="admin"){           

            header("Location: dashboard.php");
           }
           elseif ($user['role']="teacher") {        

            header("Location: teacher_dashboard.php");
           }
           if($user['role']="student"){           

            header("Location: student_dashboard.php");
           }
           
            exit;
        } else {
            $error = "Incorrect password!";
        }
    } else {
        $error = "Email not found!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - School Portal</title>
</head>
<body>
    <h2>Login</h2>

    <?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>

    <form method="POST">
        <input type="email" name="email" placeholder="Email" required><br><br>
        <input type="password" name="password" placeholder="Password" required><br><br>
        <button type="submit" name="login">Login</button>
    </form>
    <br>
    <a href="register.php">Don't have an account? Register</a>
</body>
</html>