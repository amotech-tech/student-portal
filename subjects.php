<?php
session_start();
include "config.php";

// Only admin can access
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: dashboard.php");
    exit;
}

// Add new subject
if(isset($_POST['add_subject'])){
    $name = $_POST['name'];
    $teacher_id = $_POST['teacher_id'];

    $sql = "INSERT INTO subjects (name, teacher_id) VALUES ('$name', '$teacher_id')";
    if($conn->query($sql)){
        $success = "Subject added successfully!";
    } else {
        $error = "Error: ".$conn->error;
    }
}

// Delete subject
if(isset($_GET['delete'])){
    $subject_id = $_GET['delete'];
    $conn->query("DELETE FROM subjects WHERE id='$subject_id'");
    $success = "Subject deleted successfully!";
}

// Edit subject
if(isset($_POST['edit_subject'])){
    $subject_id = $_POST['subject_id'];
    $name = $_POST['name'];
    $teacher_id = $_POST['teacher_id'];

    $conn->query("UPDATE subjects SET name='$name', teacher_id='$teacher_id' WHERE id='$subject_id'");
    $success = "Subject updated successfully!";
}

// Fetch all subjects
$subjects = $conn->query("SELECT s.id, s.name, s.teacher_id, u.fullname AS teacher_name
                          FROM subjects s
                          LEFT JOIN teachers t ON s.teacher_id = t.id
                          LEFT JOIN users u ON t.user_id = u.id");

$teachers = $conn->query("SELECT t.id, u.fullname FROM teachers t JOIN users u ON t.user_id = u.id");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Subjects</title>
</head>
<body>
<h2>Subjects Management</h2>

<?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
<?php if(isset($success)) echo "<p style='color:green;'>$success</p>"; ?>

<!-- Add Subject Form -->
<h3>Add New Subject</h3>
<form method="POST">
    <input type="text" name="name" placeholder="Subject Name" required><br><br>
    <select name="teacher_id">
        <option value="">-- Assign Teacher (Optional) --</option>
        <?php while($t = $teachers->fetch_assoc()): ?>
            <option value="<?php echo $t['id']; ?>"><?php echo $t['fullname']; ?></option>
        <?php endwhile; ?>
    </select><br><br>
    <button type="submit" name="add_subject">Add Subject</button>
</form>

<hr>

<!-- List of Subjects -->
<h3>All Subjects</h3>
<table border="1" cellpadding="5" cellspacing="0">
    <tr>
        <th>ID</th>
        <th>Subject Name</th>
        <th>Teacher</th>
        <th>Actions</th>
    </tr>
    <?php while($row = $subjects->fetch_assoc()): ?>
    <tr>
        <td><?php echo $row['id']; ?></td>
        <td><?php echo $row['name']; ?></td>
        <td><?php echo $row['teacher_name'] ? $row['teacher_name'] : 'None'; ?></td>
        <td>
            <a href="subjects.php?edit=<?php echo $row['id']; ?>">Edit</a> |
            <a href="subjects.php?delete=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

<hr>

<!-- Edit Subject Form -->
<?php
if(isset($_GET['edit'])){
    $edit_id = $_GET['edit'];
    $res = $conn->query("SELECT * FROM subjects WHERE id='$edit_id'");
    if($res->num_rows == 1){
        $edit_subject = $res->fetch_assoc();
        $teachers = $conn->query("SELECT t.id, u.fullname FROM teachers t JOIN users u ON t.user_id = u.id");
    }
}
?>

<?php if(isset($edit_subject)): ?>
<h3>Edit Subject</h3>
<form method="POST">
    <input type="hidden" name="subject_id" value="<?php echo $edit_subject['id']; ?>">
    <input type="text" name="name" value="<?php echo $edit_subject['name']; ?>" required><br><br>
    <select name="teacher_id">
        <option value="">-- Assign Teacher (Optional) --</option>
        <?php while($t = $teachers->fetch_assoc()): ?>
            <option value="<?php echo $t['id']; ?>" <?php if($t['id']==$edit_subject['teacher_id']) echo 'selected'; ?>>
                <?php echo $t['fullname']; ?>
            </option>
        <?php endwhile; ?>
    </select><br><br>
    <button type="submit" name="edit_subject">Update Subject</button>
</form>
<?php endif; ?>

<br><a href="dashboard.php">Back to Dashboard</a>
</body>
</html>