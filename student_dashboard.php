<?php
session_start();
include "config.php";

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student'){
    header("Location: login.php");
    exit;
}

$student_id = $_SESSION['user_id'];

/* Total Subjects */
$subjects = $conn->query("SELECT COUNT(*) as total FROM grades WHERE student_id='$student_id'");
$total_subjects = $subjects->fetch_assoc()['total'] ?? 0;

/* Average Marks */
$avg = $conn->query("SELECT AVG(marks) as average FROM grades WHERE student_id='$student_id'");
$average_marks = round($avg->fetch_assoc()['average'] ?? 0,1);

/* Attendance Percentage */
$att = $conn->query("SELECT 
    SUM(CASE WHEN status='Present' THEN 1 ELSE 0 END) as present,
    COUNT(*) as total 
    FROM attendance WHERE student_id='$student_id'");
$att_data = $att->fetch_assoc();
$attendance_percent = ($att_data['total'] > 0) 
    ? round(($att_data['present']/$att_data['total'])*100,1) 
    : 0;

/* Recent Grades */
$recent_grades = $conn->query("
    SELECT subjects.subject_name, grades.marks
    FROM grades
    JOIN subjects ON grades.subject_id = subjects.id
    WHERE grades.student_id='$student_id'
    ORDER BY grades.id DESC LIMIT 5
");
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Student Dashboard</title>
<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Segoe UI',sans-serif;}

body{
    display:flex;
    background:#f4f6f9;
    min-height:100vh;
}

/* Sidebar */
.sidebar{
    width:230px;
    background:#492828;
    color:white;
    padding:20px;
}

.sidebar h2{
    margin-bottom:30px;
    text-align:center;
}

.sidebar a{
    display:block;
    color:white;
    padding:10px;
    margin-bottom:8px;
    text-decoration:none;
    border-radius:6px;
}

.sidebar a:hover{
    background:#656D3F;
}

/* Main */
.main{
    flex:1;
    padding:25px;
}

/* Topbar */
.topbar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:25px;
}

.profile{
    display:flex;
    align-items:center;
    gap:10px;
}

.profile img{
    width:40px;
    height:40px;
    border-radius:50%;
    object-fit:cover;
}

/* Cards */
.cards{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(200px,1fr));
    gap:20px;
    margin-bottom:25px;
}

.card{
    background:white;
    padding:20px;
    border-radius:12px;
    box-shadow:0 5px 15px rgba(0,0,0,0.05);
}

.card h3{
    font-size:14px;
    color:#777;
    margin-bottom:10px;
}

.card p{
    font-size:22px;
    font-weight:bold;
    color:#492828;
}

/* Table */
.table-card{
    background:white;
    padding:20px;
    border-radius:12px;
    box-shadow:0 5px 15px rgba(0,0,0,0.05);
    margin-bottom:25px;
}

table{
    width:100%;
    border-collapse:collapse;
}

th,td{
    padding:10px;
    text-align:left;
    border-bottom:1px solid #eee;
}

th{
    background:#f9f9f9;
}

/* Quick actions */
.actions a{
    display:inline-block;
    padding:10px 15px;
    background:#84934A;
    color:white;
    text-decoration:none;
    border-radius:6px;
    margin-right:10px;
    margin-top:10px;
}

.actions a:hover{
    background:#656D3F;
}

@media(max-width:768px){
    .sidebar{display:none;}
}
</style>
</head>
<body>

<div class="sidebar">
    <h2>Student Panel</h2>
    <a href="#">Dashboard</a>
    <a href="view_grades.php">My Grades</a>
    <a href="view_attendance.php">Attendance</a>
    <a href="profile.php">My Profile</a>
    <a href="logout.php">Logout</a>
</div>

<div class="main">

    <div class="topbar">
        <h2>Welcome, <?php echo $_SESSION['fullname']; ?></h2>
        <div class="profile">
            <img src="uploads/<?php echo $_SESSION['profile_picture'] ?? 'default.png'; ?>">
        </div>
    </div>

    <div class="cards">
        <div class="card">
            <h3>Total Subjects</h3>
            <p><?php echo $total_subjects; ?></p>
        </div>

        <div class="card">
            <h3>Average Marks</h3>
            <p><?php echo $average_marks; ?>%</p>
        </div>

        <div class="card">
            <h3>Attendance</h3>
            <p><?php echo $attendance_percent; ?>%</p>
        </div>
    </div>

    <div class="table-card">
        <h3 style="margin-bottom:15px;">Recent Grades</h3>
        <table>
            <tr>
                <th>Subject</th>
                <th>Marks</th>
            </tr>
            <?php while($row = $recent_grades->fetch_assoc()){ ?>
            <tr>
                <td><?php echo $row['subject_name']; ?></td>
                <td><?php echo $row['marks']; ?>%</td>
            </tr>
            <?php } ?>
        </table>
    </div>

    <div class="table-card">
        <h3>Quick Actions</h3>
        <div class="actions">
            <a href="view_grades.php">View Full Report</a>
            <a href="view_attendance.php">Check Attendance</a>
            <a href="profile.php">Edit Profile</a>
        </div>
    </div>

</div>

</body>
</html>