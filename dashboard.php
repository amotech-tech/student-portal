<?php
session_start();
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$fullname = $_SESSION['fullname'];
$role = $_SESSION['role'];
?>

<h2>Welcome, <?php echo $fullname; ?>!</h2>

<?php if($role == 'admin'): ?>
    <a href="students.php">Manage Students</a>
    <a href="teachers.php">Manage Teachers</a>
    <a href="classes.php">Manage Classes</a>
    <a href="subjects.php">Manage Subjects</a>
    <a href="grades.php">Grades</a>
    <a href="attendance.php">Attendance</a>
<?php elseif($role == 'teacher'): 
      header("Location: teacher_dashboard.php");?>?>
    
<?php elseif($role == 'student'): 
    header("Location: student_dashboard.php");?>
<?php endif; ?>

<a href="logout.php">Logout</a>