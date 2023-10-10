<!DOCTYPE html>
<html>
<head>
<title>Profile</title>
<link rel="stylesheet" href="replay.css"/>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
     integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
     crossorigin=""/>

</head>
<body>
<?php
    if(isset($_GET)){
        if(isset($_GET['id'])){
            $id = $_GET['id'];

     require_once 'includes/dbh.inc.php';
     //fetches replay data from database
     $stmt = $pdo->prepare("SELECT lat,lng, round_stage, panoramas.`cubemap-filename`,player_one_lat,player_one_lng FROM rounds JOIN panoramas ON rounds.panoramaID_one = panoramas.panoramaID WHERE matchID = :id;");
     $stmt->bindValue(":id",$id);
     $stmt->execute();
    $roundsPlayerOne = $stmt->fetchAll();
    //if there is nothing in that, the replay cant exist!
    if(count($roundsPlayerOne) == 0){
        echo "This replay does not exist";
        die();
       }
    $stmt = $pdo->prepare("SELECT lat,lng, round_stage, panoramas.`cubemap-filename`,player_two_lat,player_two_lng FROM rounds JOIN panoramas ON rounds.panoramaID_two = panoramas.panoramaID WHERE matchID = :id;");
    $stmt->bindValue(":id",$id);
    $stmt->execute();
   $roundsPlayerTwo = $stmt->fetchAll();

    //displays round data for replay
    foreach($roundsPlayerOne as $round){
        $stage = $round['round_stage'];
        echo "<div class='playerOne $stage filename' >" . $round['cubemap-filename'] . "</div>";
        echo "<div class='playerOne $stage lat' >" . $round['player_one_lat']. "</div>";
        echo "<div class='playerOne $stage lng' >" . $round['player_one_lng']. "</div>";
        echo "<div class='playerOne $stage answer-lat' >" . $round['lat']. "</div>";
        echo "<div class='playerOne $stage answer-lng' >" . $round['lng']. "</div>";
    }
    foreach($roundsPlayerTwo as $round){
        $stage = $round['round_stage'];
        echo "<div class='playerTwo $stage filename' >" . $round['cubemap-filename'] . "</div>";
        echo "<div class='playerTwo $stage lat' >" . $round['player_two_lat']. "</div>";
        echo "<div class='playerTwo $stage lng' >" . $round['player_two_lng']. "</div>";
        echo "<div class='playerTwo $stage answer-lat' >" . $round['lat']. "</div>";
        echo "<div class='playerTwo $stage answer-lng' >" . $round['lng']. "</div>";
    }
    }
}
echo "<div class='panorama-container'>";
echo "</div>";
?>

<div class="continue-container">
<button id="continue" class="hidden">Continue</button>
</div>

<script src="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.js"></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g="crossorigin="anonymous"></script>   
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.js"></script>
<script src='panorama.js'></script>
<script src='replay.js'></script>
</body>
</html> 