<?php
session_start();
include "config.php";

// Only admin can access
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: dashboard.php");
    exit;
}

// Add new student
if(isset($_POST['add_student'])){
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $admission_no = $_POST['admission_no'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];

    // Prevent duplicate email
    $check_email = $conn->query("SELECT * FROM users WHERE email='$email'");
    if($check_email->num_rows > 0){
        $error = "This email is already registered!";
    } else {
        $sql_user = "INSERT INTO users (fullname,email,password,role) VALUES ('$fullname','$email','$password','student')";
        if($conn->query($sql_user)){
            $user_id = $conn->insert_id;
            $sql_student = "INSERT INTO students (user_id,admission_no,date_of_birth,gender) 
                            VALUES ('$user_id','$admission_no','$dob','$gender')";
            $conn->query($sql_student);
            $success = "Student added successfully!";
        } else {
            $error = "Error: ".$conn->error;
        }
    }
}

// Delete student
if(isset($_GET['delete'])){
    $student_id = $_GET['delete'];
    // Get user_id first
    $res = $conn->query("SELECT user_id FROM students WHERE id='$student_id'");
    if($res->num_rows == 1){
        $user_id = $res->fetch_assoc()['user_id'];
        $conn->query("DELETE FROM students WHERE id='$student_id'");
        $conn->query("DELETE FROM users WHERE id='$user_id'");
        $success = "Student deleted successfully!";
    }
}

// Edit student
if(isset($_POST['edit_student'])){
    $student_id = $_POST['student_id'];
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $admission_no = $_POST['admission_no'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];

    // Update users table
    $res = $conn->query("SELECT user_id FROM students WHERE id='$student_id'");
    $user_id = $res->fetch_assoc()['user_id'];

    $conn->query("UPDATE users SET fullname='$fullname', email='$email' WHERE id='$user_id'");
    $conn->query("UPDATE students SET admission_no='$admission_no', date_of_birth='$dob', gender='$gender' WHERE id='$student_id'");
    $success = "Student updated successfully!";
}

// Fetch all students
$students = $conn->query("SELECT s.id, u.fullname, u.email, s.admission_no, s.date_of_birth, s.gender
                          FROM students s
                          JOIN users u ON s.user_id = u.id");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Students</title>
</head>
<body>
<h2>Students Management</h2>

<?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
<?php if(isset($success)) echo "<p style='color:green;'>$success</p>"; ?>

<!-- Add Student Form -->
<h3>Add New Student</h3>
<form method="POST">
    <input type="text" name="fullname" placeholder="Full Name" required><br><br>
    <input type="email" name="email" placeholder="Email" required><br><br>
    <input type="password" name="password" placeholder="Password" required><br><br>
    <input type="text" name="admission_no" placeholder="Admission No" required><br><br>
    <input type="date" name="dob" required><br><br>
    <select name="gender">
        <option value="Male">Male</option>
        <option value="Female">Female</option>
    </select><br><br>
    <button type="submit" name="add_student">Add Student</button>
</form>

<hr>

<!-- List of Students -->
<h3>All Students</h3>
<table border="1" cellpadding="5" cellspacing="0">
    <tr>
        <th>ID</th>
        <th>Full Name</th>
        <th>Email</th>
        <th>Admission No</th>
        <th>DOB</th>
        <th>Gender</th>
        <th>Actions</th>
    </tr>
    <?php while($row = $students->fetch_assoc()): ?>
    <tr>
        <td><?php echo $row['id']; ?></td>
        <td><?php echo $row['fullname']; ?></td>
        <td><?php echo $row['email']; ?></td>
        <td><?php echo $row['admission_no']; ?></td>
        <td><?php echo $row['date_of_birth']; ?></td>
        <td><?php echo $row['gender']; ?></td>
        <td>
            <a href="students.php?edit=<?php echo $row['id']; ?>">Edit</a> |
            <a href="students.php?delete=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

<hr>

<!-- Edit Student Form -->
<?php
if(isset($_GET['edit'])){
    $edit_id = $_GET['edit'];
    $res = $conn->query("SELECT s.id, u.fullname, u.email, s.admission_no, s.date_of_birth, s.gender, s.user_id
                         FROM students s JOIN users u ON s.user_id = u.id WHERE s.id='$edit_id'");
    if($res->num_rows == 1){
        $edit_student = $res->fetch_assoc();
    }
}
?>

<?php if(isset($edit_student)): ?>
<h3>Edit Student</h3>
<form method="POST">
    <input type="hidden" name="student_id" value="<?php echo $edit_student['id']; ?>">
    <input type="text" name="fullname" value="<?php echo $edit_student['fullname']; ?>" required><br><br>
    <input type="email" name="email" value="<?php echo $edit_student['email']; ?>" required><br><br>
    <input type="text" name="admission_no" value="<?php echo $edit_student['admission_no']; ?>" required><br><br>
    <input type="date" name="dob" value="<?php echo $edit_student['date_of_birth']; ?>" required><br><br>
    <select name="gender">
        <option value="Male" <?php if($edit_student['gender']=='Male') echo 'selected'; ?>>Male</option>
        <option value="Female" <?php if($edit_student['gender']=='Female') echo 'selected'; ?>>Female</option>
    </select><br><br>
    <button type="submit" name="edit_student">Update Student</button>
</form>
<?php endif; ?>

<br><a href="dashboard.php">Back to Dashboard</a>
</body>
</html>