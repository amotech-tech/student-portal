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

    // Handle profile picture
    $profile_picture = 'default.png';
    if(isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error']==0){
        $ext = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
        $profile_picture = uniqid().".".$ext;
        move_uploaded_file($_FILES['profile_picture']['tmp_name'], "uploads/".$profile_picture);
    }

    // Prevent duplicate email
    $check_email = $conn->query("SELECT * FROM users WHERE email='$email'");
    if($check_email->num_rows>0){
        $error="This email is already registered!";
    } else {
        $sql_user = "INSERT INTO users (fullname,email,password,role,profile_picture) 
                     VALUES ('$fullname','$email','$password','teacher','$profile_picture')";
        if($conn->query($sql_user)){
            $user_id = $conn->insert_id;
            $conn->query("INSERT INTO teachers (user_id,department) VALUES ('$user_id','$department')");
            $conn->query("INSERT INTO activity_logs(user_id, action) VALUES('$user_id','Added new teacher')");
            $success="Teacher added successfully!";
        } else {
            $error="Error: ".$conn->error;
        }
    }
}

// Delete teacher
if(isset($_GET['delete'])){
    $teacher_id = intval($_GET['delete']);
    $res = $conn->query("SELECT user_id FROM teachers WHERE id='$teacher_id'");
    if($res->num_rows==1){
        $user_id = $res->fetch_assoc()['user_id'];
        $conn->query("DELETE FROM teachers WHERE id='$teacher_id'");
        $conn->query("DELETE FROM users WHERE id='$user_id'");
        $conn->query("INSERT INTO activity_logs(user_id, action) VALUES('$user_id','Deleted teacher')");
        $success="Teacher deleted successfully!";
    }
}

// Edit teacher
if(isset($_POST['edit_teacher'])){
    $teacher_id = intval($_POST['teacher_id']);
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $department = $_POST['department'];

    $res = $conn->query("SELECT user_id, profile_picture FROM teachers JOIN users ON teachers.user_id = users.id WHERE teachers.id='$teacher_id'");
    if($res->num_rows==1){
        $row = $res->fetch_assoc();
        $user_id = $row['user_id'];
        $profile_picture = $row['profile_picture'];

        if(isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error']==0){
            $ext = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
            $profile_picture = uniqid().".".$ext;
            move_uploaded_file($_FILES['profile_picture']['tmp_name'], "uploads/".$profile_picture);
        }

        $conn->query("UPDATE users SET fullname='$fullname', email='$email', profile_picture='$profile_picture' WHERE id='$user_id'");
        $conn->query("UPDATE teachers SET department='$department' WHERE id='$teacher_id'");
        $conn->query("INSERT INTO activity_logs(user_id, action) VALUES('$user_id','Edited teacher')");
        $success="Teacher updated successfully!";
    }
}

// Fetch all teachers
$teachers = $conn->query("SELECT t.id, u.fullname, u.email, t.department, u.profile_picture
                          FROM teachers t
                          JOIN users u ON t.user_id = u.id");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Teachers</title>
    <style>
        body{font-family:'Segoe UI',sans-serif;margin:0;background:#ECECEC;display:flex;}
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
        th,td{padding:12px;border-bottom:1px solid #ddd;text-align:left;}
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
    <a href="classes.php"> Classes</a>
    <a href="subjects.php"> Subjects</a>
    <a href="grades.php"> Grades</a>
    <a href="attendance.php"> Attendance</a>
    <a href="pending_users.php">Pending Users</a>
    <a href="logout.php">Logout</a>
</div>

<div class="main">
    <h2>Teachers Management</h2>

    <?php if(isset($error)) echo "<div class='msg-error'>$error</div>"; ?>
    <?php if(isset($success)) echo "<div class='msg-success'>$success</div>"; ?>

    <!-- Add Teacher Form -->
    <h3>Add New Teacher</h3>
    <form method="POST" enctype="multipart/form-data">
        <label>Full name:</label><input type="text" name="fullname" placeholder="Full Name" required>
       <label>Email:</label> <input type="email" name="email" placeholder="Email" required>
       <label>password:</label> <input type="password" name="password" placeholder="Password" required>
       <label>Department:</label> <input type="text" name="department" placeholder="Department" required>
        <label>Profile Picture:</label>
        <input type="file" name="profile_picture" accept="image/*">
        <button type="submit" name="add_teacher">Add Teacher</button>
    </form>

    <!-- Teachers Table -->
    <h3>All Teachers</h3>
    <table>
        <tr>
            <th>Photo</th>
            <th>ID</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Department</th>
            <th>Actions</th>
        </tr>
        <?php if($teachers->num_rows>0): ?>
            <?php while($row = $teachers->fetch_assoc()): ?>
            <tr>
                <td><img class="profile" src="uploads/<?php echo $row['profile_picture']; ?>" alt="Photo"></td>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['fullname']; ?></td>
                <td><?php echo $row['email']; ?></td>
                <td><?php echo $row['department']; ?></td>
                <td>
                    <a href="teachers.php?edit=<?php echo $row['id']; ?>" class="action-btn edit-btn">Edit</a>
                    <a href="teachers.php?delete=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure?')" class="action-btn delete-btn">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="6" style="text-align:center;">No teachers found</td></tr>
        <?php endif; ?>
    </table>

    <!-- Edit Teacher Form -->
    <?php
    if(isset($_GET['edit'])){
        $edit_id=intval($_GET['edit']);
        $res=$conn->query("SELECT t.id, u.fullname, u.email, t.department, u.profile_picture
                           FROM teachers t JOIN users u ON t.user_id=u.id WHERE t.id='$edit_id'");
        if($res->num_rows==1){
            $edit_teacher=$res->fetch_assoc();
        }
    }
    ?>
    <?php if(isset($edit_teacher)): ?>
    <h3>Edit Teacher</h3>
    <form method="POST" enctype="multipart/form-data">
        <label>name:<label>
        <input type="hidden" name="teacher_id" value="<?php echo $edit_teacher['id']; ?>">
        <input type="text" name="fullname" value="<?php echo $edit_teacher['fullname']; ?>" required>
        <label>Email:</label><input type="email" name="email" value="<?php echo $edit_teacher['email']; ?>" required>
        <label>Department:</label><input type="text" name="department" value="<?php echo $edit_teacher['department']; ?>" required>
        <label></label><label>Profile Picture:</label>
        <input type="file" name="profile_picture" accept="image/*">
        <button type="submit" name="edit_teacher">Update Teacher</button>
    </form>
    <?php endif; ?>

</div>
</body>
</html>