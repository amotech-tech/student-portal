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
<?php elseif($role == 'teacher'): ?>
    <a href="my_classes.php">My Classes</a>
    <a href="my_subjects.php">My Subjects</a>
    <a href="grades.php">Enter Grades</a>
    <a href="attendance.php">Record Attendance</a>
<?php elseif($role == 'student'): ?>
    <a href="my_grades.php">My Grades</a>
    <a href="my_attendance.php">My Attendance</a>
    <a href="my_subjects.php">My Subjects</a>
<?php endif; ?>

<a href="logout.php">Logout</a>