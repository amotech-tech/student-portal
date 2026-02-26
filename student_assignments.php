<?php
session_start();
include "config.php";

if($_SESSION['role'] != 'student'){
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['user_id'];
$student = $conn->query("SELECT * FROM users WHERE id='$student_id'")->fetch_assoc();

/* Fetch assignments for student's class */
$assignments = $conn->query("
    SELECT * FROM assignments 
    WHERE id=".$student['id']."
    ORDER BY uploaded_on DESC
");

/* Handle assignment submission */
if(isset($_POST['submit'])){
    $assignment_id = $_POST['assignment_id'];
    if(isset($_FILES['file'])){
        $filename = time().'_'.$_FILES['file']['name'];
        move_uploaded_file($_FILES['file']['tmp_name'], 'uploads/assignments/'.$filename);
        $conn->query("INSERT INTO assignment_submissions(assignment_id, student_id, file, submitted_on) 
                      VALUES('$assignment_id','$student_id','$filename',NOW())");
        echo "<p style='color:green;'>Assignment submitted successfully</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Assignments</title>
<style>
body{font-family:'Segoe UI'; background:#ECECEC; display:flex; margin:0;}
.sidebar{width:220px; background:#492828; color:white; display:flex; flex-direction:column; align-items:center; padding-top:20px;}
.sidebar img{width:80px; height:80px; border-radius:50%; object-fit:cover; margin-bottom:10px; border:2px solid #84934A;}
.sidebar h2{margin-bottom:20px; font-size:18px;}
.sidebar a{width:100%; padding:14px 20px; text-decoration:none; color:white; display:block; border-top:1px solid rgba(255,255,255,0.1);}
.sidebar a:hover{background:#656D3F;}
.main{flex:1; padding:25px;}
h1{color:#492828; margin-bottom:20px;}
.assignment-card{background:white; padding:18px; border-radius:10px; margin-bottom:15px; border-left:5px solid #84934A; box-shadow:0 2px 6px rgba(0,0,0,0.15);}
.assignment-card h3{color:#492828; margin-bottom:5px;}
.assignment-card p{margin-bottom:8px;}
.assignment-card form input[type="file"]{margin-top:5px;}
.assignment-card form button{margin-top:8px; padding:8px 14px; background:#84934A; color:white; border:none; border-radius:5px; cursor:pointer;}
.assignment-card form button:hover{background:#656D3F;}
.countdown{font-weight:bold; color:#492828; margin-bottom:8px;}
</style>
</head>
<body>

<div class="sidebar">
<img src="uploads/<?php echo $student['profile_picture']; ?>" alt="Profile">
<h2><?php echo $student['fullname']; ?></h2>
<a href="student_dashboard.php">Dashboard</a>
<a href="student_notes.php">Notes</a>
<a href="student_assignments.php">Assignments</a>
<a href="student_profile.php">Profile</a>
<a href="logout.php">Logout</a>
</div>

<div class="main">
<h1>Assignments</h1>

<?php if($assignments->num_rows>0): ?>
<?php while($a = $assignments->fetch_assoc()): ?>
<div class="assignment-card">
<h3><?php echo $a['title']; ?></h3>
<p><?php echo $a['description']; ?></p>
<p>Deadline: <?php echo date("d-M-Y H:i", strtotime($a['end_time'])); ?></p>
<p class="countdown" id="timer-<?php echo $a['id']; ?>"></p>

<?php
$now = date('Y-m-d H:i:s');
if($now < $a['end_time']):
?>
<form method="POST" enctype="multipart/form-data">
<input type="hidden" name="assignment_id" value="<?php echo $a['id']; ?>">
<input type="file" name="file" required>
<button type="submit" name="submit">Submit Assignment</button>
</form>
<?php else: ?>
<p style="color:red;">Assignment expired</p>
<?php endif; ?>
</div>
<script>
var countDownDate<?php echo $a['id']; ?> = new Date("<?php echo $a['end_time']; ?>").getTime();
var timerElement<?php echo $a['id']; ?> = document.getElementById("timer-<?php echo $a['id']; ?>");

var x<?php echo $a['id']; ?> = setInterval(function() {
  var now = new Date().getTime();
  var distance = countDownDate<?php echo $a['id']; ?> - now;
  if (distance > 0) {
    var days = Math.floor(distance / (1000 * 60 * 60 * 24));
    var hours = Math.floor((distance % (1000*60*60*24))/(1000*60*60));
    var minutes = Math.floor((distance % (1000*60*60))/(1000*60));
    var seconds = Math.floor((distance % (1000*60))/1000);
    timerElement<?php echo $a['id']; ?>.innerHTML = "Time Left: " + days + "d " + hours + "h "
      + minutes + "m " + seconds + "s ";
  } else {
    clearInterval(x<?php echo $a['id']; ?>);
    timerElement<?php echo $a['id']; ?>.innerHTML = "Assignment expired";
  }
}, 1000);
</script>
<?php endwhile; ?>
<?php else: ?>
<p>No active assignments right now.</p>
<?php endif; ?>

</div>
</body>
</html>