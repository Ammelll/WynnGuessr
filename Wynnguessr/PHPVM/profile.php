<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" media="screen" href="https://fontlibrary.org//face/minecraftia" type="text/css" />
    <link rel="stylesheet" href="profile.css" />
</head>
<body>
<?php
require_once 'includes/dbh.inc.php';
	$id = $_GET['id'];
	session_start();
	if(!isset($id)){
	 $id =$_SESSION['id'];
	}
        $stmt = $pdo->prepare("SELECT * FROM users WHERE users.id = :id");
        $stmt->bindValue(":id", ((int) $id));
        $stmt->execute();
        $validID = $stmt->fetchAll();
        if (!$validID) {
            echo "Invalid ID!";
            die();
        }
            $stmt = $pdo->prepare("SELECT username,id, `creation-time`,elo FROM users WHERE users.id = :id");
            $stmt->bindValue(":id", ((int) $id));
            $stmt->execute();
            $user_info = $stmt->fetch();
            echo "<h1>" . htmlspecialchars($user_info['username']) . "</h1>";
	echo "<h1>" . htmlspecialchars($user_info['elo']) . "</h1>";
	echo "<h1>" . htmlspecialchars($user_info['creation-time']) . "</h1>";



        include 'includes/functions.inc.php';

        echo "<h1>Match History</h1>";

        $stmt = $pdo->prepare("SELECT `time-of-play`,username,id,matchID,player_one_id,player_two_id,player_one_elo_change,player_two_elo_change,winnerID FROM users JOIN matches ON player_one_id = :id OR player_two_id = :id WHERE users.id = :id");
            $stmt->bindValue(":id", ((int) $id));
            $stmt->execute();
            $match_info = $stmt->fetchAll();
	if(!$match_info){
	echo "<h2> No Matches! </h2>";
}
        foreach ($match_info as $info) {
            if (!isset($info['player_one_elo_change'])) {
                continue;
            }
            $matchID = $info['matchID'];
            $winnerID = $info['winnerID'];
            if (isset($_SESSION)) {
                if (isset($_SESSION['id'])) {
                    $currentid = $_SESSION['id'];
                    require_once 'includes/dbh.inc.php';
                    $stmt = $pdo->prepare("SELECT `is-admin` FROM users WHERE id = :id");
                    $stmt->bindValue(":id", $currentid);
                    $stmt->execute();
                    if ($stmt->fetch()['is-admin']) {
                        echo "<form action='admin.php' method='POST'>";
                        echo "<button name='matchID' value=$matchID>Delete Match ↓</button>";
                        echo "</form>";
                    }
                }
            }
            echo "<table>";
            echo "<tr>";
            echo "<th>Player</th>";
            echo "<th>Elo Change</th>";
            echo "<th>Time of Play</th>";
            echo "</tr>";
            $player_one_id = $info['player_one_id'];
            if ($player_one_id == $winnerID) {
                echo "<tr class='winner'>";
            } else {
                echo "<tr class='loser'>";
            }
            echo "<td>" . "<a href='profile.php?id=$player_one_id'>" . getUserByID($player_one_id) . "</a>" . "</td>";
            echo "<td>" . "<a href='replay.php?id=$matchID'>" . $info['player_one_elo_change'] . "</a>" . "</td>";
            echo "<td rowspan='2' class='date'>" . "<a href='replay.php?id=$matchID'>" . $info['time-of-play'] . "</a>" . "</td>";
            echo "</tr>";
            $player_two_id = $info['player_two_id'];
            if ($player_two_id == $winnerID) {
                echo "<tr class='winner'>";
            } else {
                echo "<tr class='loser'>";
            }
            echo "<td>" . "<a href='profile.php?id=$player_two_id'>" . getUserByID($player_two_id) . "</a>" . "</td>";
            echo "<td>" . "<a href='replay.php?id=$matchID'>" . $info['player_two_elo_change'] . "</a>" . "</td>";
            echo "</tr>";
            echo "</table>";
	}
?>

</body>
</html>
