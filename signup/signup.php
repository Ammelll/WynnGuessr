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
    <link rel="stylesheet" href="signup.css">
    <title>Sign Up</title>
</head>
<body>
    <div class="signup-container">
        <div class="signup-box"> 
        <span id="signup-text">SIGN UP</span>
        <form action="../includes/signuph.inc.php" method="post">
            <input type="text" name="username" placeholder="Username">
            <input type="email" name="email"placeholder="E-Mail (Optional)">
            <input type="password" name="pwd"placeholder="Password">
            <button id="signup-button">SIGN UP</button>
        </form>
        </div>
    </div>
</body>
</html>
<?php
//displays erorr message if url contains error
    if(isset($_GET) && isset($_GET['error'])){
        if($_GET['error'] == "unset"){
            echo "<div class='signup-container'><div class='error'>One or more fields are not filled out!</div></div>";
        }
        if($_GET['error'] == "takenEmail"){
            echo "<div class='signup-container'><div class='error'>That email is already in use!</div></div>";
        }
        if($_GET['error'] == "takenID"){
            echo "<div class='signup-container'><div class='error'>That username is not available!</div></div>";
        }
    }
?>
