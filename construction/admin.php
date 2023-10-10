<?php
session_start();
if(isset($_SESSION)){
    if(isset($_SESSION['id'])){
        $id = $_SESSION['id'];

        require_once 'includes/dbh.inc.php';
        $stmt = $pdo->prepare("SELECT `is-admin` FROM users WHERE id = :id");
        $stmt->bindValue(":id",$id);
        $stmt->execute();
        //ensures user is an admin
        if($stmt->fetch()['is-admin']){
            try{
            if(isset($_POST) && isset($_POST['id']) && isset($_POST['elo'])){
                if(is_numeric($_POST['elo']) && is_numeric($_POST['id'])){
                    $stmt = $pdo->prepare("UPDATE users SET elo = :newElo WHERE id=:id");
                    $stmt->bindValue(":newElo",$_POST['elo']);
                    $stmt->bindValue(":id",$_POST['id']);
                    $stmt->execute();
                    echo "<a href='profile.php'>Elo Sucessfully updated!</a>";
                } else{
                    echo "Invalid Input!";
                }
            }
            //deletes match queued for deletion after validation
            elseif(isset($_POST) && isset($_POST['matchID'])&& is_numeric($_POST['matchID'])){
                $stmt = $pdo->prepare("DELETE FROM matches WHERE matchID = :matchID");
                $stmt->bindValue(":matchID",$_POST['matchID']);
                $stmt->execute();
                echo "<a href='profile.php'>Match Sucessfully Deleted!</a>";
            } 
            //friends queued friends after validation of input usernames
             elseif(isset($_POST) && isset($_POST['id']) && isset($_POST['username'])){
                    include 'includes/functions.inc.php';
                    $friendID = getIDByUser($_POST['username']);
                    if(!$friendID == null){
                        $stmt = $pdo->prepare("SELECT * FROM friends WHERE (requesterID = :requesterID AND receiverID = :receiverID) OR (requesterID = :receiverID AND receiverID = :requesterID)");
                        $stmt->bindValue(":requesterID",$friendID);
                        $stmt->bindValue(":receiverID",$_POST['id']);
                        $stmt->execute();
                        $alreadyFriends = $stmt->fetchAll();
                        if(!$alreadyFriends){
                            $stmt = $pdo->prepare("INSERT INTO friends (requesterID,receiverID,pending) VALUES (:id,:userID,false)");
                            $stmt->bindValue(":id",$_POST['id']);
                            $stmt->bindValue(":userID",$friendID);
                            $stmt->execute();
                            echo "<a href='profile.php'>Friend sucessfully added!</a>";
                        } else {
                            echo "This user is already friends";
                        }
                    } 
                } else{
                    echo "Invalid Input";
                }
            } catch (Exception $e){
                echo $e->getMessage();
            }
        } else{
            echo "You do not have privileges to view this page!";
        }
    }
}
?>
