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
    echo "<p class='success-msg'>Assignment posted successfully</p>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Post Online Assignment</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            color: #333;
            padding: 20px;
        }

        h2 {
            text-align: center;
            color: #2c3e50;
        }

        form {
            width: 500px;
            max-width: 90%;
            margin: 20px auto;
            background-color: #fff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        select, input[type="text"], input[type="datetime-local"], textarea {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }

        textarea {
            resize: vertical;
        }

        button {
            padding: 12px 20px;
            background-color: #a94c15;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #1f618d;
        }

        .success-msg {
            text-align: center;
            color: green;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .back-link {
            display: block;
            width: 100px;
            margin: 20px auto;
            text-align: center;
            padding: 10px 0;
            background-color: #218838;
            color: white;
            border-radius: 5px;
            text-decoration: none;
        }

        .back-link:hover {
            background-color: #8a450d;
        }
    </style>
</head>
<body>

<h2>Post Online Assignment</h2>

<form method="POST" enctype="multipart/form-data">
    <label>Select Class</label>
    <select name="class_id" required>
        <option value="">-- Select Class --</option>
        <?php while($c = $classes->fetch_assoc()){ ?>
            <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['classname']); ?></option>
        <?php } ?>
    </select>

    <label>Assignment Title</label>
    <input type="text" name="title" placeholder="Assignment Title" required>

    <label>Assignment Description</label>
    <textarea name="description" placeholder="Assignment Description" rows="4"></textarea>

    <label>Upload File (optional)</label>
    <input type="file" name="file">

    <label>Start Time</label>
    <input type="datetime-local" name="start_time" required>

    <label>End Time</label>
    <input type="datetime-local" name="end_time" required>

    <button type="submit" name="post">Post Assignment</button>
</form>

<a class="back-link" href="teacher_dashboard.php">Back</a>

</body>
</html>