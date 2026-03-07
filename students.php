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

    // Handle photo upload
    $profile_picture = 'default.png'; // default
    if(isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0){
        $ext = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
        $profile_picture = uniqid().".".$ext;
        move_uploaded_file($_FILES['profile_picture']['tmp_name'], "uploads/".$profile_picture);
    }

    // Prevent duplicate email
    $check_email = $conn->query("SELECT * FROM users WHERE email='$email'");
    if($check_email->num_rows > 0){
        $error = "This email is already registered!";
    } else {
        $sql_user = "INSERT INTO users (fullname,email,password,role,profile_picture) 
                     VALUES ('$fullname','$email','$password','student','$profile_picture')";
        if($conn->query($sql_user)){
            $user_id = $conn->insert_id;
            $sql_student = "INSERT INTO students (user_id,admission_no,date_of_birth,gender) 
                            VALUES ('$user_id','$admission_no','$dob','$gender')";
            $conn->query($sql_student);
            $conn->query("INSERT INTO activity_logs(user_id, action) VALUES('$user_id','Added new student')");
            $success = "Student added successfully!";
        } else {
            $error = "Error: ".$conn->error;
        }
    }
}

// Delete student
if(isset($_GET['delete'])){
    $student_id = intval($_GET['delete']);
    $res = $conn->query("SELECT user_id FROM students WHERE id='$student_id'");
    if($res->num_rows == 1){
        $user_id = $res->fetch_assoc()['user_id'];
        $conn->query("DELETE FROM students WHERE id='$student_id'");
        $conn->query("DELETE FROM users WHERE id='$user_id'");
        $conn->query("INSERT INTO activity_logs(user_id, action) VALUES('$user_id','Deleted student')");
        $success = "Student deleted successfully!";
    }
}

// Edit student
if(isset($_POST['edit_student'])){
    $student_id = intval($_POST['student_id']);
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $admission_no = $_POST['admission_no'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];

    $res = $conn->query("SELECT user_id, profile_picture FROM students JOIN users ON students.user_id = users.id WHERE students.id='$student_id'");
    if($res->num_rows == 1){
        $row = $res->fetch_assoc();
        $user_id = $row['user_id'];
        $profile_picture = $row['profile_picture'];

        // Check if new photo uploaded
        if(isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0){
            $ext = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
            $profile_picture = uniqid().".".$ext;
            move_uploaded_file($_FILES['profile_picture']['tmp_name'], "uploads/".$profile_picture);
        }

        $conn->query("UPDATE users SET fullname='$fullname', email='$email', profile_picture='$profile_picture' WHERE id='$user_id'");
        $conn->query("UPDATE students SET admission_no='$admission_no', date_of_birth='$dob', gender='$gender' WHERE id='$student_id'");
        $conn->query("INSERT INTO activity_logs(user_id, action) VALUES('$user_id','Edited student')");
        $success = "Student updated successfully!";
    }
}

// Fetch all students
$students = $conn->query("SELECT s.id, u.fullname, u.email, s.admission_no, s.date_of_birth, s.gender, u.profile_picture
                          FROM students s
                          JOIN users u ON s.user_id = u.id");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Students</title>
    <style>
        body{font-family:'Segoe UI', sans-serif;margin:0;background:#ECECEC;display:flex;}
        h3{color:#492828;}
        .sidebar{width:230px;background:#492828;color:white;min-height:100vh;padding-top:20px;}
        .sidebar h2{text-align:center;padding-bottom:20px;}
        .sidebar a{display:block;padding:14px 20px;color:white;text-decoration:none;}
        .sidebar a:hover{background:#656D3F;}
        .main{flex:1;padding:25px;}
        form{background:white;padding:20px;border-radius:8px;box-shadow:0 4px 10px rgba(0,0,0,0.1);margin-bottom:30px;}
        input, select{width:100%;padding:10px;margin-bottom:15px;border:1px solid #ccc;border-radius:5px;}
        button{padding:10px 15px;background:#84934A;color:white;border:none;border-radius:5px;cursor:pointer;}
        button:hover{background:#656D3F;}
        table{width:100%;border-collapse:collapse;background:white;border-radius:8px;overflow:hidden;box-shadow:0 4px 10px rgba(0,0,0,0.1);}
        th, td{padding:12px;border-bottom:1px solid #ddd;text-align:left;}
        th{background:#84934A;color:white;}
        tr:hover{background:#f1f1f1;}
        .action-btn{padding:6px 12px;border:none;border-radius:5px;color:white;cursor:pointer;text-decoration:none;margin-right:5px;}
        .edit-btn{background:#656D3F;}
        .delete-btn{background:#492828;}
        .msg-success{color:green;font-weight:bold;margin-bottom:15px;}
        .msg-error{color:red;font-weight:bold;margin-bottom:15px;}
        img.profile{width:50px;height:50px;border-radius:50%;}
        @media(max-width:768px){body{flex-direction:column;}.sidebar{width:100%;}table, form{font-size:14px;}}
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Admin Panel</h2>
    <a href="dashboard.php">Dashboard</a>
    <a href="students.php"> Students</a>
    <a href="teachers.php"> Teachers</a>
    <a href="classes.php">Classes</a>
    <a href="subjects.php"> Subjects</a>
    <a href="grades.php">Grades</a>
    <a href="attendance.php"> Attendance</a>
    <a href="pending_users.php">Pending-Users</a>
    <a href="logout.php">Logout</a>
</div>

<div class="main">
    <h2>Students </h2>

    <?php if(isset($error)) echo "<div class='msg-error'>$error</div>"; ?>
    <?php if(isset($success)) echo "<div class='msg-success'>$success</div>"; ?>

    <!-- Add Student Form -->
    <h3>Add New Student</h3>
    <form method="POST" enctype="multipart/form-data">
        <label>Name:</label><input type="text" name="fullname" placeholder="Full Name" required>
        <label>Email:</label><input type="email" name="email" placeholder="Email" required>
       <label>Password:</label> <input type="password" name="password" placeholder="Password" required>
        <label>Admission Number:</label><input type="text" name="admission_no" placeholder="Admission No" required>
       <label>DOB:</label> <input type="date" name="dob" required>
       <label>Gender:</label> <select name="gender">
            <option value="Male">Male</option>
            <option value="Female">Female</option>
        </select>
        <label>Profile Picture:</label>
        <input type="file" name="profile_picture" accept="image/*">
        <button type="submit" name="add_student">Add Student</button>
    </form>

    <!-- Students Table -->
    <h3>All Students</h3>
    <table>
        <tr>
            <th>Photo</th>
            <th>ID</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Admission No</th>
            <th>DOB</th>
            <th>Gender</th>
            <th>Actions</th>
        </tr>
        <?php if($students->num_rows > 0): ?>
            <?php while($row = $students->fetch_assoc()): ?>
            <tr>
                <td><img class="profile" src="uploads/<?php echo $row['profile_picture']; ?>" alt="Photo"></td>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['fullname']; ?></td>
                <td><?php echo $row['email']; ?></td>
                <td><?php echo $row['admission_no']; ?></td>
                <td><?php echo $row['date_of_birth']; ?></td>
                <td><?php echo $row['gender']; ?></td>
                <td>
                    <a href="students.php?edit=<?php echo $row['id']; ?>" class="action-btn edit-btn">Edit</a>
                    <a href="students.php?delete=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure?')" class="action-btn delete-btn">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="8" style="text-align:center;">No students found</td></tr>
        <?php endif; ?>
    </table>

    <!-- Edit Student Form -->
    <?php
    if(isset($_GET['edit'])){
        $edit_id = intval($_GET['edit']);
        $res = $conn->query("SELECT s.id, u.fullname, u.email, s.admission_no, s.date_of_birth, s.gender, u.id, u.profile_picture
                             FROM students s JOIN users u ON s.user_id = u.id WHERE s.id='$edit_id'");
        if($res->num_rows == 1){
            $edit_student = $res->fetch_assoc();
        }
    }
    ?>
    <?php if(isset($edit_student)): ?>
    <h3>Edit Student</h3>
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="student_id" value="<?php echo $edit_student['id']; ?>">
        <label>Name:</label><input type="text" name="fullname" value="<?php echo $edit_student['fullname']; ?>" required>
        <label>Email:</label><input type="email" name="email" value="<?php echo $edit_student['email']; ?>" required>
        <label>Admission Number:</label><input type="text" name="admission_no" value="<?php echo $edit_student['admission_no']; ?>" required>
       <label>DoB:</label> <input type="date" name="dob" value="<?php echo $edit_student['date_of_birth']; ?>" required>
       <label>Gender:</label> <select name="gender">
            <option value="Male" <?php if($edit_student['gender']=='Male') echo 'selected'; ?>>Male</option>
            <option value="Female" <?php if($edit_student['gender']=='Female') echo 'selected'; ?>>Female</option>
        </select>
        <label>Profile Picture:</label>
        <input type="file" name="profile_picture" accept="image/*">
        <button type="submit" name="edit_student">Update Student</button>
    </form>
    <?php endif; ?>

</div>
</body>
</html>