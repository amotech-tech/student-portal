<?php
session_start();
include "config.php";

// Only admin access
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: dashboard.php");
    exit;
}

// Add new subject
if(isset($_POST['add_subject'])){
    $subject_name = $_POST['subject_name'];
    $class_id = $_POST['class_id'];
    $teacher_id = $_POST['teacher_id'];

    // Prevent duplicate subject for same class
    $check = $conn->query("SELECT * FROM subjects WHERE subject_name='$subject_name' AND class_id='$class_id'");
    if($check->num_rows>0){
        $error = "This subject is already assigned to the class!";
    } else {
        $conn->query("INSERT INTO subjects (subject_name, class_id, teacher_id) VALUES ('$subject_name', '$class_id', '$teacher_id')");
        $success = "Subject added successfully!";
    }
}

// Delete subject
if(isset($_GET['delete'])){
    $subject_id = intval($_GET['delete']);
    $conn->query("DELETE FROM subjects WHERE id='$subject_id'");
    $success = "Subject deleted successfully!";
}

// Edit subject
if(isset($_POST['edit_subject'])){
    $subject_id = intval($_POST['subject_id']);
    $subject_name = $_POST['subject_name'];
    $class_id = $_POST['class_id'];
    $teacher_id = $_POST['teacher_id'];

    $conn->query("UPDATE subjects SET name='$subject_name', id='$class_id', teacher_id='$teacher_id' WHERE id='$subject_id'");
    $success = "Subject updated successfully!";
}

// Fetch all subjects
$subjects = $conn->query("SELECT s.id, s.name AS subject_name, c.name, u.fullname AS teacher_name
                          FROM subjects s
                          LEFT JOIN classes c ON s.id=c.id
                          LEFT JOIN users u ON s.teacher_id=u.id");

// Fetch classes & teachers for dropdown
$classes = $conn->query("SELECT id, name FROM classes");
$teachers = $conn->query("SELECT id, fullname FROM users WHERE role='teacher'");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Subjects</title>
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
    <a href="students.php">Students</a>
    <a href="teachers.php">Teachers</a>
    <a href="classes.php"> Classes</a>
    <a href="subjects.php"> Subjects</a>
    <a href="grades.php"> Grades</a>
    <a href="attendance.php">Attendance</a>
    <a href="pending_users.php">Pending Users</a>
    <a href="logout.php">Logout</a>
</div>

<div class="main">
    <h2>Subjects Management</h2>

    <?php if(isset($error)) echo "<div class='msg-error'>$error</div>"; ?>
    <?php if(isset($success)) echo "<div class='msg-success'>$success</div>"; ?>

    <!-- Add Subject Form -->
    <h3>Add New Subject</h3>
    <form method="POST">
        <input type="text" name="subject_name" placeholder="Subject Name" required>
        <select name="class_id" required>
            <option value="">--Select Class--</option>
            <?php while($c=$classes->fetch_assoc()): ?>
                <option value="<?php echo $c['id']; ?>"><?php echo $c['name']; ?></option>
            <?php endwhile; ?>
        </select>
        <select name="teacher_id" required>
            <option value="">--Select Teacher--</option>
            <?php while($t=$teachers->fetch_assoc()): ?>
                <option value="<?php echo $t['id']; ?>"><?php echo $t['fullname']; ?></option>
            <?php endwhile; ?>
        </select>
        <button type="submit" name="add_subject">Add Subject</button>
    </form>

    <!-- Subjects Table -->
    <h3>All Subjects</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Subject Name</th>
            <th>Class</th>
            <th>Teacher</th>
            <th>Actions</th>
        </tr>
        <?php if($subjects->num_rows>0): ?>
            <?php while($row=$subjects->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['subject_name']; ?></td>
                <td><?php echo $row['name'] ?: 'Not assigned'; ?></td>
                <td><?php echo $row['teacher_name'] ?: 'Not assigned'; ?></td>
                <td>
                    <a href="subjects.php?edit=<?php echo $row['id']; ?>" class="action-btn edit-btn">Edit</a>
                    <a href="subjects.php?delete=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure?')" class="action-btn delete-btn">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="5" style="text-align:center;">No subjects found</td></tr>
        <?php endif; ?>
    </table>

    <!-- Edit Subject Form -->
    <?php
    if(isset($_GET['edit'])){
        $edit_id=intval($_GET['edit']);
        $res=$conn->query("SELECT * FROM subjects WHERE id='$edit_id'");
        if($res->num_rows==1){
            $edit_subject=$res->fetch_assoc();
            $classes = $conn->query("SELECT id, name FROM classes");
            $teachers = $conn->query("SELECT id, fullname FROM users WHERE role='teacher'");
        }
    }
    ?>
    <?php if(isset($edit_subject)): ?>
    <h3>Edit Subject</h3>
    <form method="POST">
        <input type="hidden" name="subject_id" value="<?php echo $edit_subject['id']; ?>">
        <input type="text" name="subject_name" value="<?php echo $edit_subject['name']; ?>" required>
        <select name="class_id" required>
            <option value="">--Select Class--</option>
            <?php while($c=$classes->fetch_assoc()): ?>
                <option value="<?php echo $c['id']; ?>" <?php if($c['id']==$edit_subject['id']) echo 'selected'; ?>><?php echo $c['name']; ?></option>
            <?php endwhile; ?>
        </select>
        <select name="teacher_id" required>
            <option value="">--Select Teacher--</option>
            <?php while($t=$teachers->fetch_assoc()): ?>
                <option value="<?php echo $t['id']; ?>" <?php if($t['id']==$edit_subject['teacher_id']) echo 'selected'; ?>><?php echo $t['fullname']; ?></option>
            <?php endwhile; ?>
        </select>
        <button type="submit" name="edit_subject">Update Subject</button>
    </form>
    <?php endif; ?>

</div>
</body>
</html>