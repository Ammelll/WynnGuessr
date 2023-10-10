<?php 
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WynnGuessr</title>
    <script src=" https://cdn.jsdelivr.net/npm/js-cookie@3.0.5/dist/js.cookie.min.js "></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/></head>
    <link rel="stylesheet" href="game.css"/>
    <script defer src="http://24.130.55.123:3001/socket.io/socket.io.js"></script>  
  <script src="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.js"></script> 
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g="crossorigin="anonymous"></script>   
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.js"></script>
    <script src='panorama.js'></script>
<script defer  src='game-new.js'></script>
</head>
<body>
<div class='countdown-container'>
    <h1 id='countdown' class='hidden'>NA</h1>
</div>
<?php
    require_once 'includes/dbh.inc.php';
    //if playertwo is connected display game page, else connect page
    if(($_SESSION['id'])){
        $playerOne =  $_SESSION['username'];
        $playerTwo = $_SESSION['playerTwo'];
        $stmt = $pdo->prepare("SELECT id,username, elo FROM users WHERE username = :usernameOne OR username = :usernameTwo");
    
        $stmt->bindValue(":usernameOne",$playerOne);
        $stmt->bindValue(":usernameTwo",$playerTwo);
    
        $stmt->execute();
        //checks if is one user game, if so, implements nessecary changes
        $players = $stmt->fetchAll();

        $stmt = $pdo->prepare("SELECT matchID FROM matches ORDER BY matchID DESC LIMIT 1;");
        $stmt->execute();
        $matchID = $stmt->fetch()['matchID'];
 
        //display connectpage if not logged in on both players
    } 
    echo "<div class='panorama-data'>";
    //could be for loop, displays panorama data for iframe and answer calculation
        $stmt = $pdo->prepare("SELECT * FROM panoramas ORDER BY RAND() LIMIT 10");
        $stmt->execute();
        $results = $stmt->fetchAll();
        for($i = 0; $i < 9; $i++){
            echo "<div class='cubemap-filename$i'>" . $results[$i]['cubemap-filename'] . "</div>";
        }



        echo "</div>";
        echo "<div class='player-data'>";
        //displays panorama data for round/match inputting
        echo "<div class='player-username'>" . $_SESSION['username']. "</div>";
        echo "<div class='player-id'>" . $_SESSION['id']. "</div>";
        echo "</div>";
        
        echo "<div class='game-data'>";
        echo $matchID;
        echo "</div>";
?>
        <div class="continue-container">
        <button id="continue" class="hidden">Continue</button>
        </div>
    <div class="panorama-container"></div>
<div id="scoreContainer">
<div id="userContainer"></div>
<div id="oppContainer"></div>
</div>
</body>
