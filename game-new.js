
const socket = io('http://24.130.55.123:3001');
const userID = $('.player-id').html();
var countdown = 15
var intervalID = null
let matchID = null;
var answerMarker = null;
$(document).ready(function() {
    socket.on('new-round-panorama', function(path) {
        clearInterval(intervalID)
	 $(".map-container").removeClass('results');
	if(answerMarker != null){
		answerMarker.removeFrom(map);
	}
   	 $("#userContainer").addClass("hidden")
   	 $("#oppContainer").addClass("hidden")
        finished = false;
        countdown = 15;
        $("#countdown").addClass('hidden')
        $("#submit").attr("disabled", true);
        if (currentMarker != null) {
            currentMarker.removeFrom(map);
        }
	console.log(path);
        $(".panorama-container").html(`<iframe id="panorama" allowfullscreen style="border-style:none;" src=${new Panorama(path).getPath()}></iframe>`);
    });
    if (typeof Cookies.get("match_id") != 'undefined') {
        matchID = Cookies.get("match_id");
	console.log(matchID);
    }
    if (typeof Cookies.get("room_uuid") != 'undefined' && matchID != null) {
        socket.emit("client-rejoin-room", Cookies.get("room_uuid"), matchID);
    }
    currentMarker = null;
    finished = false;
});
socket.on('round-end-countdown', () => {
    $("#countdown").toggleClass('hidden')
    timerTick(countdown--);
    intervalID = setInterval(function() {
        timerTick(countdown--)
    }, 1000)
})


socket.on('round-results', (results) => {
console.log(results);
    if (results.player_one_id == userID) {
        var userScore = results.player_one_score
        var oppScore = results.player_two_score
	var userTotal = results.player_one_total_score
	var oppTotal = results.player_two_total_score
    } else {
        var userScore = results.player_two_score
        var oppScore = results.player_one_score
        var userTotal = results.player_two_total_score 
        var oppTotal = results.player_one_total_score
    }
    $(".map-container").addClass('results');
console.log(results)
    answerMarker = L.marker(L.latLng(results.answerLocation[0], results.answerLocation[1])).addTo(map).bindPopup("<b>Answer Location</b>", {
            autoClose: false
        }).openPopup();;
    $("#userContainer").removeClass("hidden").html("<span>Your Round Score: " + userScore + "<br>Your Total Score: " + userTotal + "</span>");
    $("#oppContainer").removeClass("hidden").html("<span>Opponent Round Score: " + oppScore +"<br>Opponent Total Score: " + oppTotal + "</span>");
});

socket.on('match-end', () => {
    console.log('FINAL PUSH');
    window.location = "home.php";
});

$("body").append(`<div class="map-container">
    <div id='map'>    <button id="submit"disabled="true">Submit</button></div>

    </div>`);

var map = L.map('map', {
    crs: L.CRS.Simple,
    minZoom: -3,
    maxZoom: 2
});
var bounds = [
    [0, 0],
    [6485, 4091]
];
L.imageOverlay("imgs/wynnmap.png", bounds).addTo(map);
map.fitBounds(bounds);
$('.leaflet-control-attribution').hide();
map.on('click', function(ev) {
    if (!finished) {
        if (!(currentMarker == null)) {
            currentMarker.removeFrom(map);
        }
        updateMarker();
        currentMarker.addTo(map);
        console.log(currentMarker)
        $("#submit").attr("disabled", false);
    }

    function updateMarker() {
        currentMarker = L.marker(L.latLng(ev.latlng.lat, ev.latlng.lng));
    }
});

$("#submit").on("click", () => {
    finished = true;
    $("#submit").attr("disabled", true);
    latlng = {
        matchID: matchID,
        userID: userID,
        room_uuid: Cookies.get("room_uuid"),
        lat: currentMarker._latlng.lat,
        lng: currentMarker._latlng.lng
    };
	console.log(latlng);
    $("#countdown").addClass('hidden');
    socket.emit('client-round-end', latlng)
});

function timerTick(countdown) {
    $("#countdown").html(countdown);
    countdown--
    console.log(countdown)
    if (countdown == 0) {
        clearInterval(intervalID)
    }
}
