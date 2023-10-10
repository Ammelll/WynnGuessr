<?php 
    session_start();
    if(isset($_SESSION) && $_SESSION['id']){
        header('Location: ../home.php');
     }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="login.css">
    <title>Login</title>
</head>
<body>
    <div class="login-container">
        <div class="login-box"> 
        <span id="login-text">LOGIN</span>
        <form action="../includes/loginh.inc.php" method="post">
            <input type="text" name="username" placeholder="Username">
            <input type="password" name="pwd"placeholder="Password">
            <button id="login-button">LOGIN</button>
            <a href="../signup/signup.php">No account? Sign Up!</a>
        </form>
        </div>
    </div>
</body>
</html>
<?php
    if(isset($_GET) && isset($_GET['error'])){
        if($_GET['error'] == "invalid"){
            echo "<div class='login-container'><div class='error'>Invalid username or password!</div></div>";
        }
    }
?>
