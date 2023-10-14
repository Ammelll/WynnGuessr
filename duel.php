 <!DOCTYPE html>
<html>
<head>
<title>Duel</title>
<script src=" https://cdn.jsdelivr.net/npm/js-cookie@3.0.5/dist/js.cookie.min.js "></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g="crossorigin="anonymous"></script>
<script defer src="http://24.130.55.123:3001/socket.io/socket.io.js"></script>
<script defer src="duel.js"></script>
<link rel="stylesheet" href="duel.css">
<link rel="stylesheet" href="home.css">
</head>
<body>
<?php
session_start();
echo "<div class='player-id'>" . $_SESSION['id']. "</div>";
require_once 'includes/dbh.inc.php';
$gameKey = $_GET['gameKey'];
if(isset($gameKey) && onlyNumbers($gameKey)){
$gameKey = intval($gameKey);
echo "<div id='key'>". $gameKey . " </div>";
echo "<h1>Waiting for Player 2</h1>";
} else{
echo "<h1>Game Key Not Found</h1>";
}
function onlyNumbers($number){
if (preg_match("/^\d+$/", $number)) {
    return true;
} else {
    return false;
}
}
?>

</body>
</html>
