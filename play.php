<!DOCTYPE html>
<html>
<head>
<title>Play</title>
<script src=" https://cdn.jsdelivr.net/npm/js-cookie@3.0.5/dist/js.cookie.min.js "></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g="crossorigin="anonymous"></script>
<script defer src="http://24.130.55.123:3001/socket.io/socket.io.js"></script>
<script defer src="play.js"></script>
<link rel="stylesheet" href="pla.css">
</head>
<body>
<table>
<tr>
<td><button id='solo'>Solo</button></td>
<td><button id='casual'>Casual</button></td>
<td><button id='ranked'>Ranked</td>
<td><button id='duel'>Duel (Copy Link)</td>
</tr>
</table>
</body>
</html>
<?php
session_start();
echo "<div class='player-data'>";
echo "<div class='player-id' hidden>" . htmlspecialchars($_SESSION['id']) . "</div>";
echo "</div>";
?>
<title>Play</title>
    <body>
    </body>
</html>









