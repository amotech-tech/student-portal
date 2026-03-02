<?php
session_start();
include "config.php";

if($_SESSION['role'] != 'teacher'){
    header("Location: login.php");
    exit();
}

$class_id = $_GET['class_id'];

$students = $conn->query("SELECT * FROM users WHERE role='student' AND id='$class_id'");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Mark Attendance</title>
    <style>
        body{
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 0;
        }

        .container{
            width: 80%;
            margin: 40px auto;
            background: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        h2{
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        input[type="date"]{
            padding: 8px;
            margin-bottom: 20px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        table{
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table th{
            background-color: #007bff;
            color: white;
            padding: 10px;
            text-align: left;
        }

        table td{
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        table tr:hover{
            background-color: #f1f1f1;
        }

        select{
            padding: 6px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }

        button{
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 15px;
        }

        button:hover{
            background-color: #218838;
        }

        .top-bar{
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Mark Attendance</h2>

    <form method="POST">

        <div class="top-bar">
            <input type="hidden" name="class_id" value="<?php echo $class_id; ?>">
            <div>
                <label>Select-Date:</label>
                <input type="date" name="date" required>
            </div>
        </div>

        <table>
            <tr>
                <th>Student Name</th>
                <th>Status</th>
            </tr>

            <?php while($s = $students->fetch_assoc()){ ?>
            <tr>
                <td><?php echo $s['fullname']; ?></td>
                <td>
                    <select name="status[<?php echo $s['id']; ?>]">
                        <option value="Present">Present</option>
                        <option value="Absent">Absent</option>
                    </select>
                </td>
            </tr>
            <?php } ?>
        </table>

        <button type="submit" name="save">Save-Attendance</button>

    </form>
</div>

</body>
</html>