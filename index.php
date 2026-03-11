<!DOCTYPE html>
<html>
<head>
<title>School Portal</title>

<style>

body{
    margin:0;
    font-family: Arial, sans-serif;
    background-image:url("school.jpg");
    background-size:cover;
    background-position:center;
}

/* overlay */
.overlay{
    background:rgba(0,0,0,0.6);
    height:100vh;
    display:flex;
    flex-direction:column;
}

/* header */
header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:20px 60px;
    color:white;
}


   .logo{text-align:center;margin-bottom:20px;}
.logo img{width:60px;margin-bottom:10px;}
.logo h2{color:#492828;}


nav a{
    color:white;
    text-decoration:none;
    margin-left:20px;
    font-size:16px;
}

nav a:hover{
    color:#90ee90;
}

/* center section */
.hero{
    flex:1;
    display:flex;
    justify-content:center;
    align-items:center;
    text-align:center;
    color:white;
}

.hero h1{
    font-size:45px;
    margin-bottom:20px;
}

/* buttons */
.buttons{
    margin-top:30px;
}

.btn{
    padding:14px 30px;
    margin:10px;
    font-size:18px;
    border:none;
    border-radius:6px;
    cursor:pointer;
    transition:0.3s;
    text-decoration:none;
    display:inline-block;
}

/* register button */
.register{
    background:#90ee90;
    color:black;
}

/* login button */
.login{
    background:#8B4513;
    color:white;
}

/* animation */
.btn:hover{
    transform:scale(1.1);
    box-shadow:0 5px 15px rgba(0,0,0,0.4);
}

</style>

</head>

<body>

<div class="overlay">

<header>
<div class="logo">
            <img src="logo.jpg" alt="Logo">
</div>

<nav>

<a href="register.php">Register</a>
<a href="login.php">Login</a>
</nav>
</header>

<div class="hero">
<div>
<h1>Welcome to the School Portal</h1>
<p>Access your academic services quickly and easily</p>

<div class="buttons">
<a href="register.php" class="btn register">Register</a>
<a href="login.php" class="btn login">Login</a>
</div>

</div>
</div>

</div>

</body>
</html>
