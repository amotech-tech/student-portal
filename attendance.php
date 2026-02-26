<?php
include "config.php";

/* FETCH DATA */
$students = $conn->query("SELECT * FROM users WHERE role='student'");
$classes = $conn->query("SELECT * FROM classes");

/* ADD ATTENDANCE */
if(isset($_POST['mark'])) {

    $student_id = $_POST['student_id'];
    $class_id = $_POST['class_id'];
    $date = $_POST['date'];
    $status = $_POST['status'];

    $check = $conn->query("SELECT * FROM attendance 
        WHERE student_id='$student_id' 
        AND class_id='$class_id' 
        AND date='$date'");

    if($check->num_rows > 0){
        echo "<p style='color:red;'>Attendance already marked for this date</p>";
    } else {

        $conn->query("INSERT INTO attendance(student_id,class_id,date,status)
            VALUES('$student_id','$class_id','$date','$status')");

        echo "<p style='color:green;'>Attendance saved successfully</p>";
    }
}

/* DELETE */
if(isset($_GET['delete'])){
    $id = $_GET['delete'];
    $conn->query("DELETE FROM attendance WHERE id='$id'");
    header("Location: attendance.php");
}

/* UPDATE */
if(isset($_POST['update'])){

    $id = $_POST['id'];
    $status = $_POST['status'];

    $conn->query("UPDATE attendance SET status='$status' WHERE id='$id'");
    echo "<p style='color:green;'>Attendance updated</p>";
}
?>

<h2>Mark Attendance</h2>

<form method="POST">

<select name="student_id" required>
    <option value="">Select Student</option>
    <?php while($s = $students->fetch_assoc()){ ?>
        <option value="<?php echo $s['id']; ?>">
            <?php echo $s['fullname']; ?>
        </option>
    <?php } ?>
</select>

<select name="class_id" required>
    <option value="">Select Class</option>
    <?php while($c = $classes->fetch_assoc()){ ?>
        <option value="<?php echo $c['id']; ?>">
            <?php echo $c['classname']; ?>
        </option>
    <?php } ?>
</select>

<input type="date" name="date" required>

<select name="status" required>
    <option value="Present">Present</option>
    <option value="Absent">Absent</option>
</select>

<button type="submit" name="mark">Save</button>

</form>

<hr>

<h2>Attendance Records</h2>

<form method="GET">
    <input type="date" name="filter_date">
    <button type="submit">Filter</button>
</form>

<br>

<table border="1" cellpadding="8">
<tr>
    <th>Date</th>
    <th>Student</th>
    <th>Class</th>
    <th>Status</th>
    <th>Action</th>
</tr>

<?php

$where = "";

if(isset($_GET['filter_date']) && $_GET['filter_date'] != ""){
    $date = $_GET['filter_date'];
    $where = "WHERE attendance.date='$date'";
}

$result = $conn->query("
    SELECT attendance.*, users.fullname, classes.name
    FROM attendance
    JOIN users ON attendance.student_id = users.id
    JOIN classes ON attendance.class_id = classes.id
    $where
    ORDER BY date DESC
");

while($row = $result->fetch_assoc()){
?>

<tr>
<td><?php echo $row['date']; ?></td>
<td><?php echo $row['fullname']; ?></td>
<td><?php echo $row['classname']; ?></td>

<td>
<form method="POST" style="display:inline;">
    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
    <select name="status">
        <option <?php if($row['status']=="Present") echo "selected"; ?>>Present</option>
        <option <?php if($row['status']=="Absent") echo "selected"; ?>>Absent</option>
    </select>
    <button type="submit" name="update">Update</button>
</form>
</td>

<td>
    <a href="attendance.php?delete=<?php echo $row['id']; ?>" 
       onclick="return confirm('Delete record?')">
       Delete
    </a>
</td>
</tr>

<?php } ?>

</table>