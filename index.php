<!DOCTYPE html>
<html>
<head>
    <title>School Portal</title>

    <style>
        body{
            margin:0;
            padding:0;
            font-family: Arial, sans-serif;
            height:100vh;

            /* background image */
            background-image: url("school.jpg");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;

            display:flex;
            justify-content:center;
            align-items:center;
        }

        /* dark overlay so text is visible */
        .overlay{
            background: rgba(0,0,0,0.5);
            width:100%;
            height:100%;
            display:flex;
            justify-content:center;
            align-items:center;
        }

        .container{
            text-align:center;
            background-color: rgba(255,255,255,0.9);
            padding:40px;
            border-radius:10px;
        }

        h1{
            color:#4CAF50; /* light green */
            margin-bottom:30px;
        }

        .btn{
            display:inline-block;
            text-decoration:none;
            padding:12px 25px;
            margin:10px;
            border-radius:6px;
            font-size:18px;
            color:white;
        }

        .register{
            background-color:#4CAF50; /* light green */
        }

        .login{
            background-color:#8B4513; /* brown */
        }

        .btn:hover{
            opacity:0.8;
        }

    </style>

</head>

<body>

<div class="overlay">

    <div class="container">
        <h1>Welcome to School Portal</h1>

        <a href="register.php" class="btn register">Register</a>
        <a href="login.php" class="btn login">Login</a>
    </div>

</div>

</body>
</html>
