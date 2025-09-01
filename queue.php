<script src=" https://cdn.jsdelivr.net/npm/js-cookie@3.0.5/dist/js.cookie.min.js "></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g="crossorigin="anonymous"></script>   
<script defer src="https://cdn.socket.io/4.5.0/socket.io.min.js"></script>
<script defer src="queue.js"></script>
<link rel="stylesheet" href="home.css">
<button id='queue'>JOIN QUEUE</button>
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
