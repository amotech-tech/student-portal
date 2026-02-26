<?php
session_start();
include "config.php";

if($_SESSION['role'] != 'teacher'){
    header("Location: login.php");
}

$class_id = $_GET['class_id'];

/* Fetch students in this class */
$students = $conn->query("SELECT * FROM users WHERE role='student' AND id='$class_id'");
?>

<h2>Mark Attendance</h2>

<form method="POST">

<input type="hidden" name="class_id" value="<?php echo $class_id; ?>">
<input type="date" name="date" required>

<table border="1">
<tr>
    <th>Student</th>
    <th>Status</th>
</tr>

<?php while($s = $students->fetch_assoc()){ ?>
<tr>
    <td><?php echo $s['fullname']; ?></td>
    <td>
        <select name="status[<?php echo $s['id']; ?>]">
            <option value="Present">Present</option>
            <option value="Absent">Absent</option>
        </select>
    </td>
</tr>
<?php } ?>

</table>

<button type="submit" name="save">Save Attendance</button>
</form>