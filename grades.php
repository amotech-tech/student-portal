<?php
session_start();
include "config.php";

// Only admin
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: dashboard.php");
    exit;
}

// Add grade
if(isset($_POST['add_grade'])){
    $student_id = $_POST['student_id'];
    $class_id = $_POST['class_id'];
    $subject_id = $_POST['subject_id'];
    $grade = $_POST['grade'];

    $check = $conn->query("SELECT * FROM grades WHERE student_id='$student_id' AND subject_id='$subject_id'");
    if($check->num_rows>0){
        $error="Grade already exists for this student and subject!";
    } else {
        $conn->query("INSERT INTO grades (student_id,id,subject_id,grade) VALUES ('$student_id','$class_id','$subject_id','$grade')");
        $success="Grade added successfully!";
    }
}

// Delete grade
if(isset($_GET['delete'])){
    $grade_id = intval($_GET['delete']);
    $conn->query("DELETE FROM grades WHERE id='$grade_id'");
    $success="Grade deleted successfully!";
}

// Edit grade
if(isset($_POST['edit_grade'])){
    $grade_id = intval($_POST['grade_id']);
    $grade = $_POST['grade'];
    $conn->query("UPDATE grades SET grade='$grade' WHERE id='$grade_id'");
    $success="Grade updated successfully!";
}

// Fetch all grades
$grades = $conn->query("SELECT g.id, u.fullname AS student_name, c.name, s.name AS subject_name, g.grade
                        FROM grades g
                        JOIN students st ON g.student_id=st.id
                        JOIN users u ON st.user_id=u.id
                        JOIN classes c ON g.id=c.id
                        JOIN subjects s ON g.subject_id=s.id");

// Fetch students, classes, subjects
$students = $conn->query("SELECT st.id, u.fullname FROM students st JOIN users u ON st.user_id=u.id");
$classes = $conn->query("SELECT id, name FROM classes");
$subjects = $conn->query("SELECT id, name FROM subjects");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Grades</title>
    <style>
        body{font-family:'Segoe UI',        sans-serif;
        margin:0;
        background:#ECECEC;
        display:flex;}
        .sidebar{width:230px;
        background:#492828;
        color:white;
        min-height:100vh;
        padding-top:20px;}
        .sidebar h2{text-align:center;padding-bottom:20px;}
        .sidebar a{display:block;padding:14px 20px;color:white;text-decoration:none;}
        .sidebar a:hover{background:#656D3F;}
        .main{flex:1;padding:25px;}
        h3{color:#492828;}
        form{background:white;padding:20px;border-radius:8px;margin-bottom:30px;box-shadow:0 4px 10px rgba(0,0,0,0.1);}
        input, select{width:100%;padding:10px;margin-bottom:15px;border-radius:5px;border:1px solid #ccc;}
        button{padding:10px 15px;background:#84934A;color:white;border:none;border-radius:5px;cursor:pointer;}
        button:hover{background:#656D3F;}
        table{width:100%;border-collapse:collapse;
        background:white;border-radius:8px;
        overflow:hidden;box-shadow:0 4px 10px rgba(0,0,0,0.1);}
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
    <h2>Admin</h2>
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
    <h2>Grades Management</h2>
    <?php if(isset($error)) echo "<div class='msg-error'>$error</div>"; ?>
    <?php if(isset($success)) echo "<div class='msg-success'>$success</div>"; ?>

    <!-- Add Grade Form -->
    <h3>Add Grade</h3>
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
        <select name="subject_id" required>
            <option value="">--Select Subject--</option>
            <?php while($s=$subjects->fetch_assoc()): ?>
                <option value="<?php echo $s['id']; ?>"><?php echo $s['name']; ?></option>
            <?php endwhile; ?>
        </select>
        <input type="text" name="grade" placeholder="Grade (e.g. A+)" required>
        <button type="submit" name="add_grade">Add Grade</button>
    </form>

    <!-- Grades Table -->
    <h3>All Grades</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Student</th>
            <th>Class</th>
            <th>Subject</th>
            <th>Grade</th>
            <th>Actions</th>
        </tr>
        <?php 
        $grades = $conn->query("SELECT g.id, u.fullname AS student_name, c.name AS class_name, s.name, g.grade
                        FROM grades g
                        JOIN students st ON g.student_id=st.id
                        JOIN users u ON st.user_id=u.id
                        JOIN classes c ON g.id=c.id
                        JOIN subjects s ON g.subject_id=s.id");
        if($grades->num_rows>0): ?>
            <?php while($row=$grades->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['student_name']; ?></td>
                <td><?php echo $row['class_name']; ?></td>
                <td><?php echo $row['name']; ?></td>
                <td><?php echo $row['grade']; ?></td>
                <td>
                    <a href="grades.php?edit=<?php echo $row['id']; ?>" class="action-btn edit-btn">Edit</a>
                    <a href="grades.php?delete=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure?')" class="action-btn delete-btn">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="6" style="text-align:center;">No grades Available</td></tr>
        <?php endif; ?>
    </table>
</div>
</body>
</html>