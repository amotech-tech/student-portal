<?php
session_start();
include("config.php");
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'student'){
    header("Location: login.php"); exit();
}
$student_id = $_SESSION['user_id'];
$assignments = $conn->query("
    SELECT a.*, c.name as class_name 
    FROM assignments a
    JOIN classes c ON a.class_id = c.id
    JOIN student_subjects ss ON ss.subject_id = a.class_id
    WHERE ss.student_id = $student_id
    ORDER BY a.start_time DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Assignments</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body{ background:#f4f6f9; }
        .sidebar{ height:100vh; background:#1e293b; color:white; padding-top:20px; }
        .sidebar a{ color:#cbd5e1; text-decoration:none; display:block; padding:12px 20px; }
        .sidebar a:hover{ background:#334155; color:white; }
        .card{ border:none; border-radius:15px; box-shadow:0 4px 10px rgba(0,0,0,0.05); }
         .logo{text-align:center;margin-bottom:20px;}
.logo img{width:60px;margin-bottom:10px;}
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-2 sidebar">
              <div class="logo">
            <img src="logo.jpg" alt="Logo">
            
        </div>
            <h4 class="text-center mb-4">
                <i class="fa-solid fa-user-graduate"></i> Student
            </h4>
          

            <a href="student_dashboard.php"><i class="fa-solid fa-gauge"></i> Dashboard</a>
            <a href="student_subjects.php"><i class="fa-solid fa-book"></i> My Subjects</a>
            <a href="student_results.php"><i class="fa-solid fa-chart-line"></i> Results</a>
            <a href="student_grades.php"><i class="fa-solid fa-chart-line"></i>grades</a>
            <a href="student_notes.php"><i class="fa-solid fa-chart-line"></i> notes</a>
            <a href="student_assignments.php"><i class="fa-solid fa-chart-line"></i> Assignments</a>
            <a href="student_profile.php"><i class="fa-solid fa-user"></i> Profile</a>
            <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </div>

        <div class="col-md-10 p-4">
            <h3>Assignments</h3>
            <div class="card p-3 mt-3">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Class</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th>File</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($a = $assignments->fetch_assoc()){ ?>
                            <tr>
                                <td><?php echo htmlspecialchars($a['class_name']); ?></td>
                                <td><?php echo htmlspecialchars($a['title']); ?></td>
                                <td><?php echo htmlspecialchars($a['description']); ?></td>
                                <td>
                                    <?php if($a['file']){ ?>
                                        <a href="uploads/assignments/<?php echo $a['file']; ?>" target="_blank">View</a>
                                    <?php } else { echo "N/A"; } ?>
                                </td>
                                <td><?php echo date('d M Y H:i', strtotime($a['start_time'])); ?></td>
                                <td><?php echo date('d M Y H:i', strtotime($a['end_time'])); ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</body>
</html>