<?php
session_start();
include("config.php");

// Protect page
if(!isset($_SESSION['student_id'])){
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

// Get student details
$student_query = $conn->query("SELECT * FROM students WHERE id = $student_id");
$student = $student_query->fetch_assoc();

// Calculate GPA from grades table
$gpa_query = "
SELECT AVG(
    CASE 
        WHEN grade = 'A' THEN 4
        WHEN grade = 'B' THEN 3
        WHEN grade = 'C' THEN 2
        WHEN grade = 'D' THEN 1
        ELSE 0
    END
) AS gpa
FROM grades
WHERE student_id = $student_id
";

$gpa_result = $conn->query($gpa_query);
$gpa_row = $gpa_result->fetch_assoc();
$gpa = number_format($gpa_row['gpa'], 2);

// Count subjects
$subjects_query = $conn->query("SELECT COUNT(DISTINCT subject_id) as total FROM grades WHERE student_id = $student_id");
$subjects = $subjects_query->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Dashboard</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        body{
            background:#f4f6f9;
        }
        .sidebar{
            height:100vh;
            background:#1e293b;
            color:white;
            padding-top:20px;
        }
        .sidebar a{
            color:#cbd5e1;
            text-decoration:none;
            display:block;
            padding:12px 20px;
        }
        .sidebar a:hover{
            background:#334155;
            color:white;
        }
        .card{
            border:none;
            border-radius:15px;
            box-shadow:0 4px 10px rgba(0,0,0,0.05);
        }
        .icon-box{
            font-size:30px;
            padding:15px;
            border-radius:12px;
            color:white;
        }
    </style>
</head>

<body>

<div class="container-fluid">
    <div class="row">

        <!-- Sidebar -->
        <div class="col-md-2 sidebar">
            <h4 class="text-center mb-4">
                <i class="fa-solid fa-user-graduate"></i> Student
            </h4>

            <a href="#"><i class="fa-solid fa-gauge"></i> Dashboard</a>
            <a href="#"><i class="fa-solid fa-book"></i> My Subjects</a>
            <a href="#"><i class="fa-solid fa-chart-line"></i> Results</a>
            <a href="#"><i class="fa-solid fa-user"></i> Profile</a>
            <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </div>

        <!-- Main Content -->
        <div class="col-md-10 p-4">

            <!-- Top Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3>Welcome, <?php echo $student['fullname']; ?> 👋</h3>
                <span class="text-muted"><?php echo date("l, d M Y"); ?></span>
            </div>

            <!-- Summary Cards -->
            <div class="row g-4">

                <!-- GPA -->
                <div class="col-md-4">
                    <div class="card p-4">
                        <div class="d-flex align-items-center">
                            <div class="icon-box bg-primary me-3">
                                <i class="fa-solid fa-chart-line"></i>
                            </div>
                            <div>
                                <h6 class="text-muted">GPA</h6>
                                <h4><?php echo $gpa ? $gpa : "0.00"; ?></h4>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Subjects -->
                <div class="col-md-4">
                    <div class="card p-4">
                        <div class="d-flex align-items-center">
                            <div class="icon-box bg-success me-3">
                                <i class="fa-solid fa-book-open"></i>
                            </div>
                            <div>
                                <h6 class="text-muted">Total Subjects</h6>
                                <h4><?php echo $subjects; ?></h4>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Grades Count -->
                <div class="col-md-4">
                    <div class="card p-4">
                        <div class="d-flex align-items-center">
                            <div class="icon-box bg-warning me-3">
                                <i class="fa-solid fa-award"></i>
                            </div>
                            <div>
                                <h6 class="text-muted">Total Grades</h6>
                                <h4>
                                    <?php
                                    $grades_count = $conn->query("SELECT COUNT(*) as total FROM grades WHERE student_id = $student_id");
                                    echo $grades_count->fetch_assoc()['total'];
                                    ?>
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Recent Grades Table -->
            <div class="card mt-5 p-4">
                <h5 class="mb-3"><i class="fa-solid fa-list"></i> Recent Grades</h5>

                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Subject ID</th>
                            <th>Grade</th>
                            <th>Term</th>
                            <th>Year</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $grades = $conn->query("SELECT * FROM grades WHERE student_id = $student_id ORDER BY id DESC LIMIT 5");
                        while($row = $grades->fetch_assoc()){
                            echo "<tr>
                                    <td>{$row['subject_id']}</td>
                                    <td>{$row['grade']}</td>
                                    <td>{$row['term']}</td>
                                    <td>{$row['year']}</td>
                                  </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

</body>
</html>