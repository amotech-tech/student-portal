<?php
session_start();
include "config.php";

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit();
}

/* Approve or Deny */
if(isset($_GET['action']) && isset($_GET['id'])){
    $id = intval($_GET['id']);
    if($_GET['action'] == 'approve'){
        $conn->query("UPDATE users SET status='approved' WHERE id=$id");
    } elseif($_GET['action'] == 'deny'){
        $conn->query("UPDATE users SET status='denied' WHERE id=$id");
    }
    header("Location: pending_users.php");
    exit();
}

/* Fetch pending users */
$pending = $conn->query("SELECT * FROM users WHERE status='pending'");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Pending Users - Admin</title>
    <style>
        body{
            font-family:'Segoe UI', sans-serif;
            margin:0;
            background:#ECECEC;
            display:flex;
        }

        /* Sidebar */
.sidebar{
    width:230px;
    background:#492828;
    color:white;
    min-height:100vh;
}

.sidebar h2{
    text-align:center;
    padding:20px 0;
}

.sidebar a{
    display:block;
    padding:14px 20px;
    color:white;
    text-decoration:none;
}

.sidebar a:hover{
    background:#656D3F;
}

        /* Main content */
        .main{
            flex:1;
            padding:25px;
        }

      

        table{
            width:100%;
            border-collapse:collapse;
            background:white;
            border-radius:8px;
            overflow:hidden;
            box-shadow:0 4px 10px rgba(0,0,0,0.1);
        }

        th, td{
            padding:12px;
            border-bottom:1px solid #ddd;
            text-align:left;
        }

        th{
            background:#84934A;
            color:white;
        }

        tr:hover{
            background:#f1f1f1;
        }

        .action-btn{
            padding:6px 12px;
            border:none;
            border-radius:5px;
            color:white;
            cursor:pointer;
            text-decoration:none;
            margin-right:5px;
        }

        .approve{
            background:#656D3F;
        }

        .deny{
            background:#492828;
        }
    </style>
</head>
<body>


<div class="sidebar">
    <h2>Admin Panel</h2>
    <a href="dashboard.php">Dashboard</a>
    <a href="students.php">Students</a>
    <a href="teachers.php">Teachers</a>
    <a href="classes.php">Classes</a>
    <a href="subjects.php">Subjects</a>
    <a href="grades.php">Grades</a>
    <a href="attendance.php">Attendance</a>
    <a href="pending_users.php">Pending Users</a>
    <a href="logout.php">Logout</a>
</div>

<div class="main">
    <h2>Pending User Approvals</h2>

    <table>
        <tr>
            <th>#</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Action</th>
        </tr>

        <?php $count=1; while($row = $pending->fetch_assoc()): ?>
        <tr>
            <td><?php echo $count++; ?></td>
            <td><?php echo $row['fullname']; ?></td>
            <td><?php echo $row['email']; ?></td>
            <td><?php echo ucfirst($row['role']); ?></td>
            <td>
                <a href="pending_users.php?action=approve&id=<?php echo $row['id']; ?>" class="action-btn approve">✅ Approve</a>
                <a href="pending_users.php?action=deny&id=<?php echo $row['id']; ?>" class="action-btn deny">❌ Deny</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

</body>
</html>