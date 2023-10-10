<?php 

    require_once 'functions.inc.php';
     $username = $_POST["username"];
     $pwd = $_POST["pwd"];
     $email = $_POST["email"];
     try{
         require_once "dbh.inc.php";

        //selects email and username
         $stmt = $pdo->prepare("SELECT username, email FROM users WHERE username = :username OR email = :email;");

         $stmt->bindValue(":username",$username);
         $stmt->bindValue(":email",$email);

         $stmt->execute();

         $results = $stmt->fetch();
         //ensures fields are not already in databse or are empty
        if(empty_field($username,$pwd)){
            die();
        }
        if(takenID($username, $results)){
            die();
        }
        if(takenEmail($email, $results)){
            die();
        }        
        //makes the process take longer (prevents spamming)
        $options = [
            'cost' => 12
        ];
        //hashes pwd
        $hashedpwd = password_hash($pwd, PASSWORD_DEFAULT,$options);
        //inputs new user into  db
        $stmt = $pdo->prepare("INSERT INTO users (username, pwd, email) VALUES (:username, :hashedpwd, :email);");
        $stmt->bindValue(":username", $username);
        $stmt->bindValue(":hashedpwd", $hashedpwd);
        $stmt->bindValue(":email", $email);
        $stmt->execute();
       

        $pdo = null;    
        $stmt = null;
        header('Location: ../login/login.php');
        die();
     } catch (PDOException $e){
         die("Query Failed". $e->getMessage()); 
     }
