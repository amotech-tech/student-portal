<?php
session_start();
include "config.php";

if($_SESSION['role'] != 'teacher'){
    header("Location: login.php");
    exit();
}

$teacher_id = $_SESSION['user_id'];
$classes = $conn->query("SELECT * FROM classes WHERE teacher_id='$teacher_id'");

if(isset($_POST['post'])){
    $class_id = $_POST['class_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

    $file_name = null;
    if(isset($_FILES['file']) && $_FILES['file']['name'] != ''){
        $file_name = time().'_'.$_FILES['file']['name'];
        move_uploaded_file($_FILES['file']['tmp_name'], 'uploads/assignments/'.$file_name);
    }

    $conn->query("INSERT INTO assignments(class_id, teacher_id, title, description, file, start_time, end_time)
                  VALUES('$class_id','$teacher_id','$title','$description','$file_name','$start_time','$end_time')");
    echo "<p style='color:green;'>Assignment posted successfully</p>";
}
?>

<h2>Post Online Assignment</h2>

<form method="POST" enctype="multipart/form-data">
    <select name="class_id" required>
        <option value="">Select Class</option>
        <?php while($c = $classes->fetch_assoc()){ ?>
            <option value="<?php echo $c['id']; ?>"><?php echo $c['classname']; ?></option>
        <?php } ?>
    </select>
    <br><br>
    <input type="text" name="title" placeholder="Assignment Title" required>
    <br><br>
    <textarea name="description" placeholder="Assignment Description" rows="4"></textarea>
    <br><br>
    <input type="file" name="file">
    <br><br>
    Start Time: <input type="datetime-local" name="start_time" required>
    <br><br>
    End Time: <input type="datetime-local" name="end_time" required>
    <br><br>
    <button type="submit" name="post">Post</button>
</form>