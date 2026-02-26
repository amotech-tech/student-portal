<?php
session_start();
include "config.php";

// Only admin
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: dashboard.php");
    exit;
}

// Add attendance
if(isset($_POST['add_attendance'])){
    $student_id = $_POST['student_id'];
    $class_id = $_POST['class_id'];
    $status = $_POST['status'];
    $date = $_POST['date'];

    $check = $conn->query("SELECT * FROM attendance WHERE student_id='$student_id' AND class_id='$class_id' AND date='$date'");
    if($check->num_rows>0){
        $error="Attendance already marked for this student today!";
    } else {
        $conn->query("INSERT INTO attendance (student_id,class_id,date,status) VALUES ('$student_id','$class_id','$date','$status')");
        $success="Attendance added successfully!";
    }
}

// Delete attendance
if(isset($_GET['delete'])){
    $att_id = intval($_GET['delete']);
    $conn->query("DELETE FROM attendance WHERE id='$att_id'");
    $success="Attendance deleted successfully!";
}

// Edit attendance
if(isset($_POST['edit_attendance'])){
    $att_id = intval($_POST['att_id']);
    $status = $_POST['status'];
    $conn->query("UPDATE attendance SET status='$status' WHERE id='$att_id'");
    $success="Attendance updated successfully!";
}

// Fetch students & classes
$students = $conn->query("SELECT st.id, u.fullname FROM students st JOIN users u ON st.user_id=u.id");
$classes = $conn->query("SELECT id, name FROM classes");

// Fetch all attendance
$attendance = $conn->query("SELECT a.id, u.fullname AS student_name, c.name, a.date, a.status
                            FROM attendance a
                            JOIN students st ON a.student_id=st.id
                            JOIN users u ON st.user_id=u.id
                            JOIN classes c ON a.class_id=c.id");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Attendance</title>
    <style>
        body{font-family:'Segoe UI',sans-serif;margin:0;background:#ECECEC;display:flex;}
        .sidebar{width:230px;background:#492828;color:white;min-height:100vh;padding-top:20px;}
        .sidebar h2{text-align:center;padding-bottom:20px;}
        .sidebar a{display:block;padding:14px 20px;color:white;text-decoration:none;}
        .sidebar a:hover{background:#656D3F;}
        .main{flex:1;padding:25px;}
        h3{color:#492828;}
        form{background:white;padding:20px;border-radius:8px;margin-bottom:30px;box-shadow:0 4px 10px rgba(0,0,0,0.1);}
        input, select{width:100%;padding:10px;margin-bottom:15px;border-radius:5px;border:1px solid #ccc;}
        button{padding:10px 15px;background:#84934A;color:white;border:none;border-radius:5px;cursor:pointer;}
        button:hover{background:#656D3F;}
        table{width:100%;border-collapse:collapse;background:white;border-radius:8px;overflow:hidden;box-shadow:0 4px 10px rgba(0,0,0,0.1);}
        th,td{padding:12px;border-bottom:1px solid #ddd;text-align:left;}
        th{background:#84934A;color:white;}
        .action-btn{padding:6px 12px;border:none;border-radius:5px;color:white;text-decoration:none;margin-right:5px;}
        .edit-btn{background:#656D3F;}
        .delete-btn{background:#492828;}
        .msg-success{color:green;font-weight:bold;margin-bottom:15px;}
        .msg-error{color:red;font-weight:bold;margin-bottom:15px;}
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
    <h2>Attendance Management</h2>
    <?php if(isset($error)) echo "<div class='msg-error'>$error</div>"; ?>
    <?php if(isset($success)) echo "<div class='msg-success'>$success</div>"; ?>

    <!-- Add Attendance -->
    <h3>Add Attendance</h3>
    <form method="POST">
        <select name="student_id" required>
            <option value="">--Select Student--</option>
            <?php while($st=$students->fetch_assoc()): ?>
                <option value="<?php echo $st['id']; ?>"><?php echo $st['fullname']; ?></option>
            <?php endwhile; ?>
        </select>
        <select name="class_id" required>
            <option value="">--Select Class--</option>
            <?php while($c=$classes->fetch_assoc()): ?>
                <option value="<?php echo $c['id']; ?>"><?php echo $c['name']; ?></option>
            <?php endwhile; ?>
        </select>
        <input type="date" name="date" required>
        <select name="status" required>
            <option value="">--Select Status--</option>
            <option value="Present">Present</option>
            <option value="Absent">Absent</option>
            <option value="Late">Late</option>
        </select>
        <button type="submit" name="add_attendance">Add Attendance</button>
    </form>

    <!-- Attendance Table -->
    <h3>All Attendance</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Student</th>
            <th>Class</th>
            <th>Date</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        <?php 
        $attendance = $conn->query("SELECT a.id, u.fullname AS student_name, c.name, a.date, a.status
                            FROM attendance a
                            JOIN students st ON a.student_id=st.id
                            JOIN users u ON st.user_id=u.id
                            JOIN classes c ON a.class_id=c.id");
        if($attendance->num_rows>0): ?>
            <?php while($row=$attendance->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['student_name']; ?></td>
                <td><?php echo $row['name']; ?></td>
                <td><?php echo $row['date']; ?></td>
                <td><?php echo $row['status']; ?></td>
                <td>
                    <a href="attendance.php?delete=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure?')" class="action-btn delete-btn">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="6" style="text-align:center;">No attendance records found</td></tr>
        <?php endif; ?>
    </table>
</div>
</body>
</html>