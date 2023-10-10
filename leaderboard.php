<!DOCTYPE html>
<html>
<head>
<title>Leaderboard</title>
<link rel="stylesheet" href="home.css">
</head>
<body>

<?php   
     require_once 'includes/dbh.inc.php';
     //selects top 10 players ranked in elo and displays them
     $stmt = $pdo->prepare("SELECT username,id,elo FROM users ORDER BY elo DESC LIMIT 10");
     $stmt->execute();
     $leaderboard_list = $stmt->fetchAll();
     foreach($leaderboard_list as $leaderboarder){
        $lbID = $leaderboarder['id'];
        //players are links to their profile
        echo "<a href='profile.php?id=$lbID'>" . $leaderboarder['username'] . " - " . $leaderboarder['elo']. "</a><br><br>";
     }
?>

<a href="home.php">BACK</a>
</body>
</html> 
