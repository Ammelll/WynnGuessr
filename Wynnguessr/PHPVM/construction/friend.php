<?php 
    session_start();
    if(!isset($_SESSION['loggedIn'])){
        header('Location: login/login.php');
    }
?>
<!DOCTYPE html>
<html>
<head>
<title>Friend</title>
<link rel="stylesheet" href="base.css">
</head>
<body>
<?php
    try{
        //validates input is valid
        if(isset($_POST) && isset($_POST['receiver']) && is_numeric($_POST['receiver'])){
            require_once 'includes/dbh.inc.php';

            $receiver = $_POST['receiver'];
            $requester = $_SESSION['id'];
            $stmt = $pdo->prepare("SELECT * FROM friends WHERE (requesterID = :requesterID AND receiverID = :receiverID) OR (requesterID = :receiverID AND receiverID = :requesterID)");
            $stmt->bindValue(":requesterID",$requester);
            $stmt->bindValue(":receiverID",$receiver);
            $stmt->execute();
            //checks if there is already a friend request (pending or not) to specified user!
            if(count($stmt->fetchAll()) == 0){
                $stmt = $pdo->prepare("INSERT INTO friends (requesterID,receiverID) VALUES (:requesterID,:receiverID)");
                $stmt->bindValue(":requesterID",$requester);
                $stmt->bindValue(":receiverID",$receiver);
                $stmt->execute();
                echo "Your friend request was sent!";
            } else{
                echo "There is already an outgoing or incoming friend request for/from that user!";
            }
        } else{
            echo "Invalid Input!";
        }
    } catch(Exception $e){
        echo "The specified user does not exist!";
    }
?>
<a href="home.php">BACK</a>
</body>
</html> 