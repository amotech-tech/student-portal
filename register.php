<?php
include "config.php"; // Connect to database

if(isset($_POST['register'])){
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // hash password
    $role = $_POST['role'];

    // Check if email already exists
    $check = $conn->query("SELECT * FROM users WHERE email='$email'");
    if($check->num_rows > 0){
        $error = "Email already registered!";
    } else {
        $sql = "INSERT INTO users (fullname,email,password,role) VALUES ('$fullname','$email','$password','$role')";
        if($conn->query($sql)){
            $success = "Registered successfully!";
        } else {
            $error = "Error: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register - School Portal</title>
</head>
<body>
    <h2>Register</h2>

    <?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <?php if(isset($success)) echo "<p style='color:green;'>$success</p>"; ?>

    <form method="POST">
        <input type="text" name="fullname" placeholder="Full Name" required><br><br>
        <input type="email" name="email" placeholder="Email" required><br><br>
        <input type="password" name="password" placeholder="Password" required><br><br>
        <select name="role">
            <option value="student">Student</option>
            <option value="teacher">Teacher</option>
            <option value="admin">Admin</option>
        </select><br><br>
        <button type="submit" name="register">Register</button>
    </form>
    <br>
    <a href="login.php">Already have an account? Login</a>
</body>
</html>