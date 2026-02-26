<?php
session_start();
include "config.php";

if($_SESSION['role'] != 'teacher'){
    header("Location: login.php");
    exit();
}

$teacher_id = $_SESSION['user_id'];
$classes = $conn->query("SELECT * FROM classes WHERE teacher_id='$teacher_id'");

if(isset($_POST['upload'])){
    $class_id = $_POST['class_id'];
    $title = $_POST['title'];
    
    if(isset($_FILES['file'])){
        $filename = time().'_'.$_FILES['file']['name'];
        $target = 'uploads/notes/'.$filename;
        if(move_uploaded_file($_FILES['file']['tmp_name'], $target)){
            $conn->query("INSERT INTO class_notes(class_id, teacher_id, title, file) 
                          VALUES('$class_id','$teacher_id','$title','$filename')");
            echo "<p style='color:green;'>Note uploaded successfully</p>";
        } else {
            echo "<p style='color:red;'>Upload failed</p>";
        }
    }
}
?>

<h2>Upload Class Notes</h2>

<form method="POST" enctype="multipart/form-data">
    <select name="class_id" required>
        <option value="">Select Class</option>
        <?php while($c = $classes->fetch_assoc()){ ?>
            <option value="<?php echo $c['id']; ?>"><?php echo $c['classname']; ?></option>
        <?php } ?>
    </select>
    <br><br>
    <input type="text" name="title" placeholder="Note Title" required>
    <br><br>
    <input type="file" name="file" required>
    <br><br>
    <button type="submit" name="upload">Upload Note</button>
</form>