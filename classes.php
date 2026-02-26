<?php
session_start();
include "config.php";

// Only admin can access
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: dashboard.php");
    exit;
}

// Add new class
if(isset($_POST['add_class'])){
    $class_name = $_POST['class_name'];
    $class_code = $_POST['class_code'];
    $teacher_id = $_POST['teacher_id'];

    // Prevent duplicate class code
    $check = $conn->query("SELECT * FROM classes WHERE class_code='$class_code'");
    if($check->num_rows>0){
        $error="Class code already exists!";
    } else {
        $conn->query("INSERT INTO classes (name, class_code, teacher_id) VALUES ('$class_name','$class_code','$teacher_id')");
        $success="Class added successfully!";
    }
}

// Delete class
if(isset($_GET['delete'])){
    $class_id = intval($_GET['delete']);
    $conn->query("DELETE FROM classes WHERE id='$class_id'");
    $success="Class deleted successfully!";
}

// Edit class
if(isset($_POST['edit_class'])){
    $class_id = intval($_POST['class_id']);
    $class_name = $_POST['class_name'];
    $class_code = $_POST['class_code'];
    $teacher_id = $_POST['teacher_id'];

    $conn->query("UPDATE classes SET name='$class_name', class_code='$class_code', teacher_id='$teacher_id' WHERE id='$class_id'");
    $success="Class updated successfully!";
}

// Fetch all classes with teacher name
$classes = $conn->query("SELECT c.id, c.name, c.class_code, u.fullname AS teacher_name
                         FROM classes c LEFT JOIN users u ON c.teacher_id = u.id");

// Fetch teachers for dropdown
$teachers = $conn->query("SELECT id, fullname FROM users WHERE role='teacher'");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Classes</title>
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
    <h2>Classes Management</h2>

    <?php if(isset($error)) echo "<div class='msg-error'>$error</div>"; ?>
    <?php if(isset($success)) echo "<div class='msg-success'>$success</div>"; ?>

    <!-- Add Class Form -->
    <h3>Add New Class</h3>
    <form method="POST">
        <input type="text" name="class_name" placeholder="Class Name" required>
        <input type="text" name="class_code" placeholder="Class Code" required>
        <select name="teacher_id" required>
            <option value="">--Select Teacher--</option>
            <?php while($t = $teachers->fetch_assoc()): ?>
                <option value="<?php echo $t['id']; ?>"><?php echo $t['fullname']; ?></option>
            <?php endwhile; ?>
        </select>
        <button type="submit" name="add_class">Add Class</button>
    </form>

    <!-- Classes Table -->
    <h3>All Classes</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Class Name</th>
            <th>Class Code</th>
            <th>Teacher</th>
            <th>Actions</th>
        </tr>
        <?php if($classes->num_rows>0): ?>
            <?php while($row = $classes->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['name']; ?></td>
                <td><?php echo $row['class_code']; ?></td>
                <td><?php echo $row['teacher_name'] ?: 'Not assigned'; ?></td>
                <td>
                    <a href="classes.php?edit=<?php echo $row['id']; ?>" class="action-btn edit-btn">Edit</a>
                    <a href="classes.php?delete=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure?')" class="action-btn delete-btn">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="5" style="text-align:center;">No classes found</td></tr>
        <?php endif; ?>
    </table>

    <!-- Edit Class Form -->
    <?php
    if(isset($_GET['edit'])){
        $edit_id=intval($_GET['edit']);
        $res=$conn->query("SELECT * FROM classes WHERE id='$edit_id'");
        if($res->num_rows==1){
            $edit_class=$res->fetch_assoc();
            $teachers = $conn->query("SELECT id, fullname FROM users WHERE role='teacher'");
        }
    }
    ?>
    <?php if(isset($edit_class)): ?>
    <h3>Edit Class</h3>
    <form method="POST">
        <input type="hidden" name="class_id" value="<?php echo $edit_class['id']; ?>">
        <input type="text" name="class_name" value="<?php echo $edit_class['name']; ?>" required>
        <input type="text" name="class_code" value="<?php echo $edit_class['class_code']; ?>" required>
        <select name="teacher_id" required>
            <option value="">--Select Teacher--</option>
            <?php while($t = $teachers->fetch_assoc()): ?>
                <option value="<?php echo $t['id']; ?>" <?php if($t['id']==$edit_class['teacher_id']) echo 'selected'; ?>><?php echo $t['fullname']; ?></option>
            <?php endwhile; ?>
        </select>
        <button type="submit" name="edit_class">Update Class</button>
    </form>
    <?php endif; ?>

</div>
</body>
</html>