<?php 
    session_start();
require_once 'functions.inc.php';
//gets inputted username and pwd
     $username = $_POST["username"];
     $pwd = $_POST["pwd"];
     try{
         require_once "../includes/dbh.inc.php";
        //selects correct pwd and username from inputted username
         $stmt = $pdo->prepare("SELECT pwd, username, id FROM users WHERE username = :username;");

         $stmt->bindParam(":username",$username);

         $stmt->execute();

         $results = $stmt->fetch();
         $pdo = null;
         $stmt = null;
        //compares results with input in functions.inc.php
         verify_password($username,$pwd,$results['pwd'], $results['id']);
         die();
     } catch (PDOException $e){
         die("Query Failed". $e->getMessage()); 
     }
