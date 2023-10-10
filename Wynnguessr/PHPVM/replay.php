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
     $stmt = $pdo->prepare("SELECT rounds.lat,rounds.lng, round_stage, panoramas.`cubemap-filename`,panoramas.lat AS pat, panoramas.lng AS png FROM rounds JOIN panoramas ON rounds.panoramaID = panoramas.panoramaID WHERE matchID = :id;");
     $stmt->bindValue(":id",$id);
     $stmt->execute();
    $rounds = $stmt->fetchAll();
    if(count($rounds) == 0){
        echo "This replay does not exist";
        die();
       }
    foreach($rounds as $round){
        $stage = $round['round_stage'];
        echo "<div class='hidden $stage filename' >" . $round['cubemap-filename'] . "</div>";
        echo "<div class='hidden $stage lat' >" . $round['lat']. "</div>";
        echo "<div class='hidden $stage lng' >" . $round['lng']. "</div>";
	echo "<div class='hidden $stage answer-lat' >" . $round['pat']. "</div>";
	echo "<div class='hidden $stage answer-lng' >" . $round['png']. "</div>";    
}
    }
}
echo "<div class='panorama-container'>";
echo "</div>";
?>

<div class="continue-container">
<button id="continue">Continue</button>
</div>

<script src="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.js"></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g="crossorigin="anonymous"></script>   
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.js"></script>
<script src='panorama.js'></script>
<script src='replay.js'></script>
</body>
</html> 
