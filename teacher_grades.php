<?php
session_start();
include "config.php";

if($_SESSION['role'] != 'teacher'){
    header("Location: login.php");
    exit();
}

$class_id = $_GET['class_id'];
$teacher_id = $_SESSION['user_id'];

/* Verify teacher owns this class */
$verify = $conn->query("SELECT * FROM classes 
                        WHERE id='$class_id' 
                        AND teacher_id='$teacher_id'");

if($verify->num_rows == 0){
    die("Unauthorized access");
}

/* Fetch students in this class */
$students = $conn->query("SELECT * FROM users 
                          WHERE role='student' 
                          AND id='$class_id'");

/* Fetch subjects */
$subjects = $conn->query("SELECT * FROM subjects");

/* Save grade */
if(isset($_POST['save'])){

    $student_id = $_POST['student_id'];
    $subject_id = $_POST['subject_id'];
    $marks = $_POST['marks'];

    $check = $conn->query("SELECT * FROM grades 
        WHERE student_id='$student_id' 
        AND subject_id='$subject_id'");

    if($check->num_rows > 0){
        echo "<p style='color:red;'>Grade already exists</p>";
    } else {

        $conn->query("INSERT INTO grades(student_id,subject_id,marks)
                      VALUES('$student_id','$subject_id','$marks')");

        echo "<p style='color:green;'>Grade saved</p>";
    }
}
?>
<style>

body{
    font-family: Arial, sans-serif;
    background:#f4f6f9;
    margin:0;
    padding:40px;
}

.container{
    width:700px;
    margin:auto;
    background:white;
    padding:30px;
    border-radius:8px;
    box-shadow:0 4px 10px rgba(0,0,0,0.1);
}

h2,h3{
    text-align:center;
    color:#333;
}

form{
    margin-top:20px;
}

select,input{
    width:100%;
    padding:10px;
    margin:10px 0;
    border:1px solid #ccc;
    border-radius:5px;
}

button{
    width:100%;
    padding:12px;
    border:none;
    background:#007bff;
    color:white;
    font-size:16px;
    border-radius:5px;
    cursor:pointer;
}

button:hover{
    background:#0056b3;
}

table{
    width:100%;
    border-collapse:collapse;
    margin-top:20px;
}

table th{
    background:#007bff;
    color:white;
    padding:10px;
}

table td{
    padding:10px;
    text-align:center;
    border-bottom:1px solid #ddd;
}

table tr:hover{
    background:#f1f1f1;
}

.back{
    display:block;
    margin-top:20px;
    text-align:center;
    text-decoration:none;
    color:#007bff;
    font-weight:bold;
}

.back:hover{
    text-decoration:underline;
}

</style>

<div class="container">

<h2>Manage Grades</h2>

<form method="POST">

<select name="student_id" required>
<option value="">Select Student</option>
<?php while($s = $students->fetch_assoc()){ ?>
<option value="<?php echo $s['id']; ?>">
<?php echo $s['fullname']; ?>
</option>
<?php } ?>
</select>

<select name="subject_id" required>
<option value="">Select Subject</option>
<?php while($sub = $subjects->fetch_assoc()){ ?>
<option value="<?php echo $sub['id']; ?>">
<?php echo $sub['name']; ?>
</option>
<?php } ?>
</select>

<input type="number" name="marks" placeholder="Enter marks" required>

<button type="submit" name="save">Save Grade</button>

</form>

<hr>

<h3>Existing Grades</h3>

<table>
<tr>
<th>Student</th>
<th>Subject</th>
<th>Grade</th>
</tr>

<?php
$result = $conn->query("
    SELECT grades.*, users.fullname, subjects.name
    FROM grades
    JOIN users ON grades.student_id = users.id
    JOIN subjects ON grades.subject_id = subjects.id
    WHERE users.id='$class_id'
");

while($row = $result->fetch_assoc()){
?>

<tr>
<td><?php echo $row['fullname']; ?></td>
<td><?php echo $row['name']; ?></td>
<td><?php echo $row['grade']; ?></td>
</tr>

<?php } ?>

</table>

<a href="my_classes.php" class="back">← Back to My Classes</a>

</div>