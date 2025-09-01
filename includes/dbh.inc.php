<?php 
//credentials
$dsn = "mysql:host=localhost;dbname=wynnguessr";
$dbusername = "root";
$dbpassword = "";

try{
    //connects to db
    $pdo = new PDO($dsn,$dbusername,$dbpassword);
} catch (PDOException $e){
    echo "Connection Failed..." . $e->getMessage();
}
