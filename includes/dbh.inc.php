<?php 
//credentials
$dsn = "mysql:host=127.0.0.1;dbname=wynnguessr";
$dbusername = "newuser";
$dbpassword = "password";

try{
    //connects to db
    $pdo = new PDO($dsn,$dbusername,$dbpassword);
} catch (PDOException $e){
    echo "Connection Failed..." . $e->getMessage();
}
