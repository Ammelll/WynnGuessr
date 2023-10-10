<?php
//similar to accept.php but removes from table instead of updating pending!
if(isset($_POST) && isset($_GET)){
    if(isset($_POST['reject']) &&  isset($_GET['id'])){
        $uid = $_POST['reject'];
        $oid = $_GET['id'];
        require_once 'includes/dbh.inc.php';
        $stmt = $pdo->prepare("DELETE FROM friends WHERE requesterID = :oid AND receiverID = :uid OR requesterID = :uid AND receiverID= :oid");
        $stmt->bindValue(":oid",$oid);
        $stmt->bindValue(":uid",$uid);
        $stmt->execute();
    }
}
echo "<a href='profile.php'>" . "Go back!" . "</a>";
?>