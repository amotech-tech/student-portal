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

<!DOCTYPE html>
<html>
<head>
    <title>Upload Class Notes</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            margin: 0;
            padding: 20px;
        }
        h2 {
            color: #333;
            text-align: center;
        }
        form {
            background: #fff;
            padding: 20px;
            max-width: 500px;
            margin: 20px auto;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        select, input[type="text"], input[type="file"], button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
        }
        button {
            background: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            font-weight: bold;
        }
        button:hover {
            background: #45a049;
        }
        p {
            text-align: center;
            font-weight: bold;
        }
        p[style*="color:green"] {
            color: #28a745 !important;
        }
        p[style*="color:red"] {
            color: #dc3545 !important;
        }
    </style>
</head>
<body>

    <h2>Upload Class Notes</h2>

    <form method="POST" enctype="multipart/form-data">
        <select name="class_id" required>
            <option value="">Select Class</option>
            <?php while($c = $classes->fetch_assoc()){ ?>
                <option value="<?php echo $c['id']; ?>"><?php echo $c['classname']; ?></option>
            <?php } ?>
        </select>

        <input type="text" name="title" placeholder="Note Title" required>

        <input type="file" name="file" required>

        <button type="submit" name="upload">Upload Note</button>
    </form>

</body>
</html>
