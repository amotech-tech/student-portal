<?php
session_start();
include "config.php";

// Only admin can access
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: dashboard.php");
    exit;
}

// Add new class
if(isset($_POST['add_class'])){
    $name = $_POST['name'];
    $teacher_id = $_POST['teacher_id'];

    $sql = "INSERT INTO classes (name, teacher_id) VALUES ('$name', '$teacher_id')";
    if($conn->query($sql)){
        $success = "Class added successfully!";
    } else {
        $error = "Error: ".$conn->error;
    }
}

// Delete class
if(isset($_GET['delete'])){
    $class_id = $_GET['delete'];
    $conn->query("DELETE FROM classes WHERE id='$class_id'");
    $success = "Class deleted successfully!";
}

// Edit class
if(isset($_POST['edit_class'])){
    $class_id = $_POST['class_id'];
    $name = $_POST['name'];
    $teacher_id = $_POST['teacher_id'];

    $conn->query("UPDATE classes SET name='$name', teacher_id='$teacher_id' WHERE id='$class_id'");
    $success = "Class updated successfully!";
}

// Fetch all classes
$classes = $conn->query("SELECT c.id, c.name, c.teacher_id, u.fullname AS teacher_name
                         FROM classes c
                         LEFT JOIN teachers t ON c.teacher_id = t.id
                         LEFT JOIN users u ON t.user_id = u.id");
$teachers = $conn->query("SELECT t.id, u.fullname FROM teachers t JOIN users u ON t.user_id = u.id");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Classes</title>
</head>
<body>
<h2>Classes Management</h2>

<?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
<?php if(isset($success)) echo "<p style='color:green;'>$success</p>"; ?>

<!-- Add Class Form -->
<h3>Add New Class</h3>
<form method="POST">
    <input type="text" name="name" placeholder="Class Name" required><br><br>
    <select name="teacher_id">
        <option value="">-- Assign Teacher (Optional) --</option>
        <?php while($t = $teachers->fetch_assoc()): ?>
            <option value="<?php echo $t['id']; ?>"><?php echo $t['fullname']; ?></option>
        <?php endwhile; ?>
    </select><br><br>
    <button type="submit" name="add_class">Add Class</button>
</form>

<hr>

<!-- List of Classes -->
<h3>All Classes</h3>
<table border="1" cellpadding="5" cellspacing="0">
    <tr>
        <th>ID</th>
        <th>Class Name</th>
        <th>Teacher</th>
        <th>Actions</th>
    </tr>
    <?php while($row = $classes->fetch_assoc()): ?>
    <tr>
        <td><?php echo $row['id']; ?></td>
        <td><?php echo $row['name']; ?></td>
        <td><?php echo $row['teacher_name'] ? $row['teacher_name'] : 'None'; ?></td>
        <td>
            <a href="classes.php?edit=<?php echo $row['id']; ?>">Edit</a> |
            <a href="classes.php?delete=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

<hr>

<!-- Edit Class Form -->
<?php
if(isset($_GET['edit'])){
    $edit_id = $_GET['edit'];
    $res = $conn->query("SELECT * FROM classes WHERE id='$edit_id'");
    if($res->num_rows == 1){
        $edit_class = $res->fetch_assoc();
        $teachers = $conn->query("SELECT t.id, u.fullname FROM teachers t JOIN users u ON t.user_id = u.id");
    }
}
?>

<?php if(isset($edit_class)): ?>
<h3>Edit Class</h3>
<form method="POST">
    <input type="hidden" name="class_id" value="<?php echo $edit_class['id']; ?>">
    <input type="text" name="name" value="<?php echo $edit_class['name']; ?>" required><br><br>
    <select name="teacher_id">
        <option value="">-- Assign Teacher (Optional) --</option>
        <?php while($t = $teachers->fetch_assoc()): ?>
            <option value="<?php echo $t['id']; ?>" <?php if($t['id']==$edit_class['teacher_id']) echo 'selected'; ?>>
                <?php echo $t['fullname']; ?>
            </option>
        <?php endwhile; ?>
    </select><br><br>
    <button type="submit" name="edit_class">Update Class</button>
</form>
<?php endif; ?>

<br><a href="dashboard.php">Back to Dashboard</a>
</body>
</html>