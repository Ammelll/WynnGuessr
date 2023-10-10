<?php
//verifies the password taken from the user with the same username is equivlant to the inputted pwd
function verify_password($username,$pwd, $results, $id){
    if(password_verify($pwd,$results)){
        //sets login session variables
        session_start();
        $_SESSION['username'] = $username;
        $_SESSION['id'] = $id;
        header('Location: ../home.php');
    }
    else{
        header("location: ../login/login.php?error=invalid");
    }
}
//same as previous but works with player two in connecth.inc.php when the playertwo is connecting to the game
function verify_password_no_redirect($username,$pwd, $results){
    if(password_verify($pwd,$results)){
        $_SESSION['playerTwo'] = $username;
    } 
    header('Location: ../game.php');
}
//ensures all fields are there on signup
function empty_field($username,$pwd){
    if(empty($username) || empty($pwd)){
        header("location: ../signup/signup.php?error=unset");
        return true;
    } else{
        header("location: ../signup/signup.php");
        return false;
    }
}
//makes sure email is not already in use on signup
function takenEmail($email, $results){
    if(strcasecmp($email,$results['email']) == 0 && $email != ""){
        header("location: ../signup/signup.php?error=takenEmail") ;
        return true;
    } else{
        header("location: ../signup/signup.php");
        return false;
    }
}
//ensures id is not already in use on signup
function takenID($username, $results){
    if(strcasecmp($username,$results['username']) == 0){
        header("location: ../signup/signup.php?error=takenID") ;
        return true;
    } else{
        header("location: ../signup/signup.php");
        return false;
    }
}
//helper function to get a username based on their id
function getUserByID($id){
    include 'dbh.inc.php';
    $stmt = $pdo->prepare("SELECT username FROM users WHERE users.id = :id");
    $stmt->bindValue(":id",$id);
    $stmt->execute();
    return $stmt->fetch()['username'];
}
//helper function to get a user id based on their username
function getIDByUser($username){
    include 'dbh.inc.php';
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username");
    $stmt->bindValue(":username",$username);
    $stmt->execute();
    $id = $stmt->fetch();
    if($id){
        return $id['id'];
    } else{
        return null;
    }

}
