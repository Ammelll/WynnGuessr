<?php
//validatess inputs 
if(isset($_POST) && isset($_GET)){
    if(isset($_POST['accept']) &&  isset($_GET['id'])){
        $uid = $_POST['accept'];
        $oid = $_GET['id'];
        require_once 'includes/dbh.inc.php';
        //update friend junction table with given ids
        $stmt = $pdo->prepare("UPDATE friends SET pending = false WHERE requesterID = :oid AND receiverID = :uid");
        $stmt->bindValue(":oid",$oid);
        $stmt->bindValue(":uid",$uid);
        $stmt->execute();
    }
}
echo "<a href='profile.php'>" . "Go back!" . "</a>";
?>