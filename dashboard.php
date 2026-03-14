<?php
session_start();
include "config.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$fullname = $_SESSION['fullname'];
$role = $_SESSION['role'];
$user_id = $_SESSION['user_id'];

if($role != 'admin'){
    header("Location: login.php");
    exit();
}

/* Counts */
$total_students = $conn->query("SELECT COUNT(*) as t FROM users WHERE role='student'")->fetch_assoc()['t'];
$total_teachers = $conn->query("SELECT COUNT(*) as t FROM users WHERE role='teacher'")->fetch_assoc()['t'];
$total_classes = $conn->query("SELECT COUNT(*) as t FROM classes")->fetch_assoc()['t'];

/* Students per class */
$class_data = $conn->query("
    SELECT c.name, COUNT(u.id) as total
    FROM classes c
    LEFT JOIN users u ON u.id = c.id AND u.role='student'
    GROUP BY c.id
");

$class_names = [];
$class_totals = [];

while($row = $class_data->fetch_assoc()){
    $class_names[] = $row['name'];
    $class_totals[] = $row['total'];
}

/* Recent Activities */
$activities = $conn->query("
    SELECT a.*, u.fullname 
    FROM activity_logs a
    JOIN users u ON a.user_id = u.id
    ORDER BY a.created_at DESC
    LIMIT 5
");
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Dashboard</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
body{
    margin:0;
    font-family:'Segoe UI';
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

/* Main */
.main{
    flex:1;
    padding:25px;
}

/* Stats */
.stats{
    display:flex;
    gap:20px;
    flex-wrap:wrap;
}

.stat-card{
    background:white;
    padding:20px;
    border-radius:10px;
    flex:1;
    min-width:180px;
    text-align:center;
    border-top:5px solid #84934A;
}

.stat-card h3{
    margin:0;
    font-size:26px;
    color:#492828;
}

/* Section */
.section{
    background:white;
    margin-top:25px;
    padding:20px;
    border-radius:10px;
}

.activity-item{
    border-bottom:1px solid #ddd;
    padding:8px 0;
}

.activity-item:last-child{
    border:none;
}
.logo{text-align:center;margin-bottom:20px;}
.logo img{width:60px;margin-bottom:10px;}
</style>
</head>
<body>

<div class="sidebar">
   
    <div class="logo">
            <img src="logo.jpg" alt="Logo">
            <h2>Admin Panel</h2>
        </div>
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

<h2>Welcome, <?php echo $fullname; ?> </h2>

<!-- Stats -->
<div class="stats">
    <div class="stat-card">
        <h3><?php echo $total_students; ?></h3>
        Students
    </div>

    <div class="stat-card">
        <h3><?php echo $total_teachers; ?></h3>
        Teachers
    </div>

    <div class="stat-card">
        <h3><?php echo $total_classes; ?></h3>
        Classes
    </div>
</div>
<a href="export_students.php" target="_blank">
    <button style="padding:10px 15px; background:#84934A; color:white; border:none; border-radius:5px; cursor:pointer;">
        Export Students PDF
    </button>
</a>

<!-- Chart -->
<div class="section">
    <h3>Students Per Class</h3>
    <canvas id="classChart"></canvas>
</div>

<!-- Activity -->
<div class="section">
    <h3>Recent Activity</h3>

    <?php while($act = $activities->fetch_assoc()): ?>
        <div class="activity-item">
            <strong><?php echo $act['fullname']; ?></strong>
            <?php echo $act['action']; ?>
            <small style="color:gray;">
                (<?php echo date("d M Y H:i", strtotime($act['created_at'])); ?>)
            </small>
        </div>
    <?php endwhile; ?>
</div>

</div>

<script>
var ctx = document.getElementById('classChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($class_names); ?>,
        datasets: [{
            label: 'Students',
            data: <?php echo json_encode($class_totals); ?>,
            backgroundColor: '#84934A'
        }]
    },
    options: {
        responsive: true
    }
});
</script>

</body>
</html>