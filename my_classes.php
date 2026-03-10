<?php
session_start();
include "config.php";

if($_SESSION['role'] != 'teacher'){
    header("Location: login.php");
    exit();
}

$teacher_id = $_SESSION['user_id'];

$classes = $conn->query("SELECT * FROM classes WHERE teacher_id='$teacher_id'");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Classes</title>
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

        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
        }

        th {
            background-color: #2980b9;
            color: white;
            text-transform: uppercase;
        }

        tr:nth-child(even) {
            background-color: #ecf0f1;
        }

        tr:hover {
            background-color: #d6eaf8;
        }

        a.button {
            display: inline-block;
            padding: 8px 15px;
            background-color: #218838;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        a.button:hover {
            background-color: #218838;
        }

        .back-link {
            display: block;
            width: 100px;
            margin: 20px auto;
            text-align: center;
            padding: 10px 0;
            background-color: #ba430f;
            color: white;
            border-radius: 5px;
            text-decoration: none;
        }

        .back-link:hover {
            background-color: #ba430f;
        }
    </style>
</head>
<body>

<h2>My Classes</h2>

<table border="1">
    <tr>
        <th>Class Name</th>
        <th>Action</th>
    </tr>

    <?php while($c = $classes->fetch_assoc()){ ?>
    <tr>
        <td><?php echo htmlspecialchars($c['name']); ?></td>
        <td>
            <a class="button" href="teacher_grades.php?class_id=<?php echo $c['id']; ?>">Manage Grades</a>
        </td>
    </tr>
    <?php } ?>
</table>

<a class="back-link" href="teacher_dashboard.php">Back</a>

</body>
</html>