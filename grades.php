<?php
session_start();
include "config.php";

if(!isset($_SESSION['user_id'])|| $_SESSION['role']!='admin'){
    header("location: dashboard.php");
    exit;
}
if(isset($_POST['add_grade'])){
    $student_id=$_POST['student-id'];
    $subject_id=$_POST['subject_id'];
    $grade=$_POST['grade'];
    $term=$_POST['term'];
    $year=$_POST['year'];

    $sql="INSERT INTO grades (student_id,subject_id,grade,term,year)
          VALUES ('$student_id','$subject_id','$grade','$term','$year')";
    
    if ($conn->query($sql)){
        $success="Grade added successfully";

    }
    else{
        $error="ERROR:".$conn->error;
    }
}
if(isset($_GET['delete'])){
    $grade_id=$_GET['delete'];
    $conn->query("DELETE FROM grades WHERE id='$grade_id'");
    $success="Grade deleted successfully!";
   
}

if(isset($_POST['edit_grade'])){
    $grade_id=$_POST['grade_id'];
    $grade=$_POST['grade'];
    $term=$_POST['term'];
    $year=$_POST['year'];

    $conn->query("UPDATE grades SET grade='$grade',term='$term',year='$year' WHERE id='$grade_id'");
    $success="Grade updated successfully!";

}

$students = $conn->query("SELECT s.id, u.fullname FROM students s JOIN users u ON s.user_id=u.id");
$subjects = $conn->query("SELECT id,name FROM subjets");
$grades= $conn->query("SELECT g.id,u.fullname,sub.name AS subject,g.grade, g.term,g.year FROM grade g JOIN students s ON g.student_id=s.id JOIN users u ON s.user_id=u.id JOIN subjects sub ON g.subject_id=sub.id");
?>


<!Doctype html>
<html>
    <head><title>Grades management</title></head>
<body>
    <h2>Grades mangement</h2>
    <?php if(isset($error)) echo "<p style='color:red;'>$error</p>";?>
     <?php if(isset($success)) echo "<p style='color:green;'>$success</p>";?>
    <h3> ADD grade Form</h3>
    <form method="POST">
        <select name="student_id" required>
            <option value="">--select student--</option> 
            <?php while($s=$students->fetch_assoc());?>
            <option value="<?php echo $s['id']; ?>">
                <?php echo $s['fullname'];?>
            
            </option>
            <?php endwhile; ?>
        </select><br><br>


        <select name="subject_id" requiered>
            <option value="">--select subject--</option>
            <?php while($sub=4subjects->fetch_assoc()); ?>
            <option value="<?php echo &sub['id']; ?>">
                <?php echo $sub['name']; ?>
            </option>
            <?php endwhile;?>

        </select><br><br>
        <input type="text" name="grade" placeholder="Grade (A,B+,C...)" required><br><br>
        <select name="term" required>
            <option value="Term 1">Term 1</option>
            <option value="Term 2">Term 2</option>
            <option value="Term 3">Term 3</option>
        </select><br><br>

        <input type="number" name="year" placeholder="year (eg 2026)" required> <br><br>
        <button type ="submit" name="add_grade"> Add grade</button>
        </form>
        <hr>
         

        <h3>All Grades</h3>
        <table border="1" cellpadding="5">
            <tr>
                <th>ID</th>
                <th>Student</th>
                <th>subject</th>
                <th>GraDe</th>
                <th>Term</th>
                <th>Year</th>
            <th>Actions</th>
            </tr>
            <?php while($row =$grades->fetch_assoc()); ?>
            <tr>
                <td><?php echo $row['id'];?></td>
                    <td><?php echo $row['fullname'];?></td>
                        <td><?php echo $row['subject'];?></td>
                            <td><?php echo $row['grade'];?></td>
                                <td><?php echo $row['term'];?></td>
                                    <td><?php echo $row['year'];?></td>
                                    <td>
                                        <a href="grades.php?edit=<?php echo $row['id']; ?>"> Edit</a>|
                                         <a href="grades.php?delete=<?php echo $row['id']; ?>"> onclick="return confirm('Delete this grade?')">Delete</a>
                                    </td>
            </tr>
            <?php endwhile;?>
        </table>
        <hr>

        <?php 
        if(isset($_GET['edit'])){
            $edit_id=$_GET['edit'];
            $res= $conn->query("SELECT * FROM grades WHERE id='$edit_id'");
            if($res->num_rows==1){
                $edit_grade=$res->fetch_assoc();
            }
        }
        ?>
        <?php if(isset($edit_grade));?>
        <h3>Edit grade</h3>
        <form method="POST">
            <input type="hidden" name="grade_id" value="<?php echo $edit_grade['id'];?>">
            <input type="text" name="grade" value="<?php echo $edit_grade['grade']; ?> required"><br><br>

            <select name="term">
                <option value="Term 1" <?php if($edit_grade['term']=="Term 1") echo "selected"; ?>>Term 1</select>
                     <option value="Term 2" <?php if($edit_grade['term']=="Term 2") echo "selected"; ?>>Term 2</select>
                         <option value="Term 3" <?php if($edit_grade['term']=="Term 2") echo "selected"; ?>>Term 3</select>
            </select><br><br>
            <input type="number" name="year" value="<?php echo $edit_grade['year'];?>" required><br><br>
            <button type="submit" name="edit_grade">Update Grade</button>
        </form>
        <?php endif;?>
        <br><a href="dashboard.php"> Back to dashboard</a>
</body>
</html>