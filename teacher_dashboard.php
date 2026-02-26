<?php
session_start();
include "config.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'teacher') {
    header("Location: login.php");
    exit();
}

$teacher_id = $_SESSION['user_id'];
$teacher = $conn->query("SELECT * FROM users WHERE id='$teacher_id'")->fetch_assoc();
$classCount = $conn->query("SELECT COUNT(*) as total FROM classes WHERE teacher_id='$teacher_id'")->fetch_assoc()['total'];
$studentCount = $conn->query("
    SELECT COUNT(*) as total
    FROM users
    WHERE role='student'
    AND id IN (SELECT id FROM classes WHERE teacher_id='$teacher_id')
")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Teacher Dashboard</title>
    <style>
        /* BASE */
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #ECECEC;
            display: flex;
            height:100vh;
        }

        /* SIDEBAR */
        .sidebar {
            width: 240px;
            background: #492828;
            color: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-top: 30px;
        }
        .sidebar img {
            width:90px;
            height:90px;
            border-radius:50%;
            object-fit:cover;
            margin-bottom:12px;
            border:2px solid #84934A;
        }
        .sidebar h2 {
            margin-bottom:25px;
            font-size:20px;
            text-align:center;
            color: #ECECEC;
        }
        .sidebar a {
            width:100%;
            padding:14px 20px;
            text-decoration:none;
            color: white;
            border-top: 1px solid rgba(255,255,255,0.1);
            font-size:16px;
        }
        .sidebar a:hover {
            background: #656D3F;
        }

        /* MAIN */
        .main {
            flex:1;
            padding:25px;
        }
        .main h1 {
            color: #492828;
            margin-bottom:20px;
        }

        .cards {
            display:flex;
            gap:20px;
            flex-wrap:wrap;
        }

        .card {
            flex:1 1 280px;
            background:white;
            padding:22px;
            border-radius:12px;
            border-left:5px solid #84934A;
            box-shadow: 0 2px 6px rgba(0,0,0,0.15);
        }
        .card h3 {
            color:#492828;
            margin-bottom:10px;
            font-size:18px;
        }
        .card p {
            font-size:28px;
            font-weight:bold;
            color:#656D3F;
        }

        .btn {
            display:inline-block;
            padding:10px 18px;
            background:#84934A;
            color:white;
            border-radius:6px;
            text-decoration:none;
            margin-top:12px;
            font-size:15px;
        }
        .btn:hover {
            background:#656D3F;
        }

        @media(max-width:800px){
            .cards { flex-direction:column; }
        }
    </style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <img src="uploads/<?php echo $teacher['profile_picture']; ?>" alt="Profile">
    <h2><?php echo $teacher['fullname']; ?></h2>
    <a href="teacher_dashboard.php">Dashboard</a>
    <a href="my_classes.php">My Classes</a>
    <a href="teacher_notes.php">Upload Notes</a>
    <a href="teacher_assignments.php">Assignments</a>
    <a href="teacher_profile.php">My Profile</a>
    <a href="logout.php">Logout</a>
</div>

<!-- MAIN -->
<div class="main">
    <h1>Teacher Dashboard</h1>

    <div class="cards">
        <div class="card">
            <h3>Total Classes</h3>
            <p><?php echo $classCount; ?></p>
        </div>
        <div class="card">
            <h3>Total Students</h3>
            <p><?php echo $studentCount; ?></p>
        </div>
        <div class="card">
            <h3>Quick Actions</h3>
            <a class="btn" href="my_classes.php">My Classes</a>
            <a class="btn" href="teacher_notes.php">Notes</a>
        </div>
    </div>
</div>

</body>
</html>