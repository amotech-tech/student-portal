<?php
session_start();
include "config.php";

// Only admin can access
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: dashboard.php");
    exit;
}

// Add new teacher
if(isset($_POST['add_teacher'])){
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $department = $_POST['department'];
    $hire_date = $_POST['hire_date'];

    // Prevent duplicate email
    $check_email = $conn->query("SELECT * FROM users WHERE email='$email'");
    if($check_email->num_rows > 0){
        $error = "This email is already registered!";
    } else {
        $sql_user = "INSERT INTO users (fullname,email,password,role) VALUES ('$fullname','$email','$password','teacher')";
        if($conn->query($sql_user)){
            $user_id = $conn->insert_id;
            $sql_teacher = "INSERT INTO teachers (user_id, department, hire_date) VALUES ('$user_id','$department','$hire_date')";
            $conn->query($sql_teacher);
            $success = "Teacher added successfully!";
        } else {
            $error = "Error: ".$conn->error;
        }
    }
}

// Delete teacher
if(isset($_GET['delete'])){
    $teacher_id = $_GET['delete'];
    $res = $conn->query("SELECT user_id FROM teachers WHERE id='$teacher_id'");
    if($res->num_rows == 1){
        $user_id = $res->fetch_assoc()['user_id'];
        $conn->query("DELETE FROM teachers WHERE id='$teacher_id'");
        $conn->query("DELETE FROM users WHERE id='$user_id'");
        $success = "Teacher deleted successfully!";
    }
}

// Edit teacher
if(isset($_POST['edit_teacher'])){
    $teacher_id = $_POST['teacher_id'];
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $department = $_POST['department'];
    $hire_date = $_POST['hire_date'];

    $res = $conn->query("SELECT user_id FROM teachers WHERE id='$teacher_id'");
    $user_id = $res->fetch_assoc()['user_id'];

    $conn->query("UPDATE users SET fullname='$fullname', email='$email' WHERE id='$user_id'");
    $conn->query("UPDATE teachers SET department='$department', hire_date='$hire_date' WHERE id='$teacher_id'");
    $success = "Teacher updated successfully!";
}

// Fetch all teachers
$teachers = $conn->query("SELECT t.id, u.fullname, u.email, t.department, t.hire_date
                          FROM teachers t
                          JOIN users u ON t.user_id = u.id");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Teachers</title>
</head>
<body>
<h2>Teachers Management</h2>

<?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
<?php if(isset($success)) echo "<p style='color:green;'>$success</p>"; ?>

<!-- Add Teacher Form -->
<h3>Add New Teacher</h3>
<form method="POST">
    <input type="text" name="fullname" placeholder="Full Name" required><br><br>
    <input type="email" name="email" placeholder="Email" required><br><br>
    <input type="password" name="password" placeholder="Password" required><br><br>
    <input type="text" name="department" placeholder="Department" required><br><br>
    <input type="date" name="hire_date" placeholder="Hire Date" required><br><br>
    <button type="submit" name="add_teacher">Add Teacher</button>
</form>

<hr>

<!-- List of Teachers -->
<h3>All Teachers</h3>
<table border="1" cellpadding="5" cellspacing="0">
    <tr>
        <th>ID</th>
        <th>Full Name</th>
        <th>Email</th>
        <th>Department</th>
        <th>Hire Date</th>
        <th>Actions</th>
    </tr>
    <?php while($row = $teachers->fetch_assoc()): ?>
    <tr>
        <td><?php echo $row['id']; ?></td>
        <td><?php echo $row['fullname']; ?></td>
        <td><?php echo $row['email']; ?></td>
        <td><?php echo $row['department']; ?></td>
        <td><?php echo $row['hire_date']; ?></td>
        <td>
            <a href="teachers.php?edit=<?php echo $row['id']; ?>">Edit</a> |
            <a href="teachers.php?delete=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

<hr>

<!-- Edit Teacher Form -->
<?php
if(isset($_GET['edit'])){
    $edit_id = $_GET['edit'];
    $res = $conn->query("SELECT t.id, u.fullname, u.email, t.department, t.hire_date, t.user_id
                         FROM teachers t JOIN users u ON t.user_id = u.id WHERE t.id='$edit_id'");
    if($res->num_rows == 1){
        $edit_teacher = $res->fetch_assoc();
    }
}
?>

<?php if(isset($edit_teacher)): ?>
<h3>Edit Teacher</h3>
<form method="POST">
    <input type="hidden" name="teacher_id" value="<?php echo $edit_teacher['id']; ?>">
    <input type="text" name="fullname" value="<?php echo $edit_teacher['fullname']; ?>" required><br><br>
    <input type="email" name="email" value="<?php echo $edit_teacher['email']; ?>" required><br><br>
    <input type="text" name="department" value="<?php echo $edit_teacher['department']; ?>" required><br><br>
    <input type="date" name="hire_date" value="<?php echo $edit_teacher['hire_date']; ?>" required><br><br>
    <button type="submit" name="edit_teacher">Update Teacher</button>
</form>
<?php endif; ?>

<br><a href="dashboard.php">Back to Dashboard</a>
</body>
</html>