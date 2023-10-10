<?php
session_start();
if (!isset($_SESSION['loggedIn'])) {
    header('Location: login/login.php');
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Profile</title>
    <style>
    </style>
    <link rel="stylesheet" media="screen" href="https://fontlibrary.org//face/minecraftia" type="text/css" />
    <link rel="stylesheet" href="profile.css" />
</head>

<body>
    <?php

    //if user is not viewing their own profile... 
    if (isset($_GET) && isset($_GET['id']) && ($_GET['id'] != $_SESSION['id'])) {
        //dislpays user based on ?id=
        $id = $_GET['id'];
        require_once 'includes/dbh.inc.php';
        $stmt = $pdo->prepare("SELECT * FROM users WHERE users.id = :id");
        $stmt->bindValue(":id", ((int) $id));
        $stmt->execute();
        $validID = $stmt->fetchAll();
        if (!$validID) {
            echo "Invalid ID!";
            die();
        }
        $stmt = $pdo->prepare("SELECT `time-of-play`,username,id,`creation-time`,elo,matchID,player_one_id,player_two_id,player_one_elo_change,player_two_elo_change,winnerID FROM users JOIN matches ON player_one_id = :id OR player_two_id = :id WHERE users.id = :id");
        $stmt->bindValue(":id", ((int) $id));
        $stmt->execute();
        $user_info = $stmt->fetchAll();
        if (count($user_info) != 0) {
            echo "<h1>" . htmlspecialchars($user_info[0]['username']) . "</h1>";
            echo "<p>" . "Rating: " . htmlspecialchars($user_info[0]['elo']) . "</p>";
            //admin to check to display admin features (common use)
            if (isset($_SESSION)) {
                if (isset($_SESSION['id'])) {
                    $currentid = $_SESSION['id'];

                    require_once 'includes/dbh.inc.php';
                    $stmt = $pdo->prepare("SELECT `is-admin` FROM users WHERE id = :id");
                    $stmt->bindValue(":id", $currentid);
                    $stmt->execute();
                    $userID = $_GET['id'];
                    if ($stmt->fetch()['is-admin']) {
                        echo "<form action='admin.php' method='POST'>";
                        echo "<input name='elo' type='number' placeholder='New Elo'>";
                        echo "<button name='id' value=$userID>UPDATE</button>";
                        echo "</form>";
                    }
                }
            }
            echo "<p>" . "Account Creation Date: " . htmlspecialchars($user_info[0]['creation-time']) . "</p>";
            $receiverID = $user_info[0]['id'];
        } else {
            $stmt = $pdo->prepare("SELECT username,id, `creation-time`,elo FROM users WHERE users.id = :id");
            $stmt->bindValue(":id", ((int) $id));
            $stmt->execute();
            $user_info = $stmt->fetchAll();
            echo "<h1>" . htmlspecialchars($user_info[0]['username']) . "</h1>";
            echo "<p>" . "Rating: " . htmlspecialchars($user_info[0]['elo']) . "</p>";
            $receiverID = $user_info[0]['id'];
        }
        //remember, this user is not the logged in user => prompts with add friened
        echo "<form action='friend.php' method='POST'>";
        echo "<button name='receiver' value='$receiverID' id='friend-button'>Add Friend</button>";
        echo "</form>";
        include 'includes/functions.inc.php';
        echo "<h1>Match History</h1>";

        foreach ($user_info as $info) {
            if (!isset($info['matchID'])) {
                echo "<p>No Matches!</p>";
            }
            //doesnt display half finished matches
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
            //displays match info (usernames link to profile rest links to match replay)
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
        $stmt = $pdo->prepare("SELECT requesterID,receiverID FROM friends WHERE pending =0 AND (receiverID = :id OR requesterID = :id)");
        $stmt->bindValue(":id", $id);
        $stmt->execute();
        $friends = $stmt->fetchAll();
        if (isset($_SESSION)) {
            if (isset($_SESSION['id'])) {
                $currentid = $_SESSION['id'];
                require_once 'includes/dbh.inc.php';
                $stmt = $pdo->prepare("SELECT `is-admin` FROM users WHERE id = :id");
                $stmt->bindValue(":id", $currentid);
                $stmt->execute();
                $userID = $_GET['id'];
                if ($stmt->fetch()['is-admin']) {
                    echo "<form action='admin.php' method='POST'>";
                    echo "<input name='username' type='text' placeholder='username'>";
                    echo "<button name='id' value=$userID class='admin-friend'>Add Friend</button>";
                    echo "</form>";
                }
            }
        }
        echo "</ul>";
        echo "<div class='friends-container'>";
        echo "<figcaption>Friends!</figcaption>";

        echo "<ul>";
        //displays incoming friends
        foreach ($friends as $incoming) {
            $requesterID = $incoming['requesterID'];
            $receiverID = $incoming['receiverID'];
            if ($requesterID == $id) {
                echo "<li>" . "<a href='profile.php?id=$receiverID'>" . htmlspecialchars(getUserByID($receiverID)) . "</a>" . "</li>";
            } else {
                echo "<li>" . "<a href='profile.php?id=$requesterID'>" . htmlspecialchars(getUserByID($requesterID)) . "</a>" . "</li>";
            }
        }
        echo "</ul>";
        echo "</div>";
        //if user is logged in and viewing their own profile!
    } else {
        $id = $_SESSION['id'];
        require_once 'includes/dbh.inc.php';
        $stmt = $pdo->prepare("SELECT elo,username,id, `creation-time` FROM users WHERE users.id = :id");
        $stmt->bindValue(":id", $id);
        $stmt->execute();
        $user_info = $stmt->fetchAll();
        echo "<h1>" . htmlspecialchars($user_info[0]['username']) . "</h1>";
        echo "<p>" . "Rating: " . htmlspecialchars($user_info[0]['elo']) . "</p>";
        //admin check to display admin features
        if (isset($_SESSION)) {
            if (isset($_SESSION['id'])) {
                $id = $_SESSION['id'];
                require_once 'includes/dbh.inc.php';
                $stmt = $pdo->prepare("SELECT `is-admin` FROM users WHERE id = :id");
                $stmt->bindValue(":id", $id);
                $stmt->execute();
                if ($stmt->fetch()['is-admin']) {
                    echo "<form action='admin.php' method='POST'>";
                    echo "<input name='elo' type='number' placeholder='New Elo'>";
                    echo "<button name='id' value=$id>UPDATE</button>";
                    echo "</form>";
                }
            }
        }


        echo "<p>" . "Account Creation Date: " . htmlspecialchars($user_info[0]['creation-time']) . "</p>";
        $stmt = $pdo->prepare("SELECT winnerID,`time-of-play`,username,id, `creation-time`,elo,matchID,player_one_id,player_two_id,player_one_elo_change,player_two_elo_change FROM users JOIN matches ON player_one_id = :id OR player_two_id = :id WHERE users.id = :id");
        $stmt->bindValue(":id", $id);
        $stmt->execute();
        $user_info = $stmt->fetchAll();
        include 'includes/functions.inc.php';
        echo "<h1>Match History</h1>";
        foreach ($user_info as $info) {
            $matchID = $info['matchID'];
            if (!isset($info['player_one_elo_change'])) {
                continue;
            }
            if (isset($_SESSION)) {
                if (isset($_SESSION['id'])) {
                    $id = $_SESSION['id'];
                    require_once 'includes/dbh.inc.php';
                    $stmt = $pdo->prepare("SELECT `is-admin` FROM users WHERE id = :id");
                    $stmt->bindValue(":id", $id);
                    $stmt->execute();
                    if ($stmt->fetch()['is-admin']) {
                        echo "<form action='admin.php' method='POST'>";
                        echo "<button name='matchID' value=$matchID>Delete Match ↓</button>";
                        echo "</form>";
                    }
                }
            }
            $winnerID = $info['winnerID'];
            echo "<table>";
            echo "<tr>";
            echo "<th>Player</th>";
            echo "<th>Elo Change</th>";
            echo "<th>Time of Play</th>";
            echo "</tr>";
            $player_one_id = $info['player_one_id'];
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
        $stmt = $pdo->prepare("SELECT receiverID FROM friends WHERE requesterID = :id AND pending = 1");
        $stmt->bindValue(":id", $id);
        $stmt->execute();
        $friends = $stmt->fetchAll();
        echo "<div class='friends-container'>";

        echo "<figcaption>Outgoing Friend Requests</figcaption>";
        echo "<ul>";
        foreach ($friends as $outgoing) {
            echo "<li>" . htmlspecialchars(getUserByID($outgoing['receiverID'])) . "</li>";
        }
        echo "</div>";
        $stmt = $pdo->prepare("SELECT requesterID FROM friends WHERE receiverID = :id AND pending =1");
        $stmt->bindValue(":id", $id);
        $stmt->execute();
        $friends = $stmt->fetchAll();
        echo "</ul>";
        echo "<div class='friends-container'>";
        echo "<figcaption>Incoming Friend Requests</figcaption>";
        echo "<ul>";
        foreach ($friends as $incoming) {
            $requesterID = $incoming['requesterID'];
            echo "<li>" . htmlspecialchars(getUserByID($requesterID)) . "</li>";
            echo "<form action='accept.php?id=$requesterID' method='POST'>";
            echo "<button name='accept' value=$id>Accept</button>";
            echo "</form>";
            echo "<form action='reject.php?id=$requesterID' method='POST'>";
            echo "<button name='reject' value=$id>Reject</button>";
            echo "</form>";
        }
        echo "</ul>";
        echo "</div>";
        $stmt = $pdo->prepare("SELECT requesterID,receiverID FROM friends WHERE pending = 0 AND (receiverID = :id OR requesterID = :id)");
        $stmt->bindValue(":id", $id);
        $stmt->execute();
        $friends = $stmt->fetchAll();
        //admin to check to display admin features (common use)
        if (isset($_SESSION)) {
            if (isset($_SESSION['id'])) {
                $id = $_SESSION['id'];
                require_once 'includes/dbh.inc.php';
                $stmt = $pdo->prepare("SELECT `is-admin` FROM users WHERE id = :id");
                $stmt->bindValue(":id", $id);
                $stmt->execute();
                if ($stmt->fetch()['is-admin']) {
                    echo "<form action='admin.php' method='POST'>";
                    echo "<input name='username' type='text' placeholder='username'>";
                    echo "<button name='id' value=$id class='admin-friend'>Add Friend</button>";
                    echo "</form>";
                }
            }
        }
        echo "</ul>";
        echo "<div class='friends-container'>";
        echo "<figcaption>Friends!</figcaption>";

        echo "<ul>";
        foreach ($friends as $incoming) {
            $requesterID = $incoming['requesterID'];
            $receiverID = $incoming['receiverID'];
            if ($requesterID == $id) {
                echo "<li>" . "<a href='profile.php?id=$receiverID'>" . htmlspecialchars(getUserByID($receiverID)) . "</a>" . "</li>";
            } else {
                echo "<li>" . "<a href='profile.php?id=$requesterID'>" . htmlspecialchars(getUserByID($requesterID)) . "</a>" . "</li>";
            }
        }
        echo "</ul>";
        echo "</div>";
        echo "<a href='home.php'>BACK</a>";
    }
    ?>
</body>

</html>