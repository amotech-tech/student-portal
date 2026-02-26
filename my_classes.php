<?php
session_start();
include "config.php";

if($_SESSION['role'] != 'teacher'){
    header("Location: login.php");
    exit();
}

$teacher_id = $_SESSION['user_id'];

$classes = $conn->query("SELECT * FROM classes WHERE teacher_id='$teacher_id'");
?>

<h2>My Classes</h2>

<table border="1" cellpadding="10">
<tr>
    <th>Class Name</th>
    <th>Action</th>
</tr>

<?php while($c = $classes->fetch_assoc()){ ?>
<tr>
    <td><?php echo $c['classname']; ?></td>
    <td>
        <a href="teacher_grades.php?class_id=<?php echo $c['id']; ?>">Manage Grades</a>
    </td>
</tr>
<?php } ?>

</table>

<a href="teacher_dashboard.php">Back to Dashboard</a>