<?php 
    //gets player two password and username
    session_start();
    require_once 'functions.inc.php';
     $username = $_POST["guest-username"] ?? null;
     $pwd = $_POST["guest-pwd"] ?? null;
     try{
        //ensures they are set
        if(isset($username) && isset($pwd)){
            require_once "../includes/dbh.inc.php";
            //selects the supposed user from the database along with their pd to compare
            $stmt = $pdo->prepare("SELECT pwd, username FROM users WHERE username = :username;");

            $stmt->bindValue(":username",$username);

            $stmt->execute();

            $results = $stmt->fetch();
            $pdo = null;
            $stmt = null;
            //ensures results is not empty => data with specified username and pwd was found
            if($results){
                verify_password_no_redirect($username,$pwd,$results['pwd']);
            }else{
                //else sends back
                header("Location: ../game.php");
            }
        } else{
            //else sends back
            header("Location: ../game.php");
        }
        die();
     } catch (PDOException $e){
         die("Query Failed". $e->getMessage()); 
     }
