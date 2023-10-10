const socket = io('http://34.132.9.169:3001');
const userID = $('.player-id').html();
var countdown = 15
var intervalID = null
let matchID = null;
let match_code = null;
$(document).ready(function() {
    socket.on('new-round-panorama', function(path) {
        $("#submit").attr("disabled", true);
        clearInterval(intervalID)
        socket.emit('client-panorama-request', userID);
        finished = false;
        countdown = 15;
        $("#countdown").toggleClass('hidden')
        $("#submit").attr("disabled", false);
        currentMarker.removeFrom(map);
        $(".panorama-container").html(`<iframe id="panorama" allowfullscreen style="border-style:none;" src=${new Panorama(path).getPath()}></iframe>`);
    });
    if (typeof Cookies.get("match_id") != 'undefined') {
        matchID = Cookies.get("match_id");
    }
    if (typeof Cookies.get("room_uuid") != 'undefined') {
        /*PREVENTED AGAINST IMPERSONATION*/
        socket.emit("client-rejoin-room", Cookies.get("room_uuid"), matchID);
    }
    if (typeof Cookies.get("match_code") != 'undefined') {
        match_code = Cookies.get("match_code");
    }
    currentMarker = null;
    finished = false;
});

socket.on('round-end-countdown', () => {
    console.log("countdown")
    $("#countdown").toggleClass('hidden')
    intervalID = setInterval(function() {
        timerTick(countdown--)
    }, 1000)
})


socket.on('match-end', () => {
    window.location = "home.php";
});

$("body").append(`<div class="map-container">
    <div id='map'></div>
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
        console.log(currentMarker._latlng)
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
        match_code: match_code,
        userID: userID,
        lat: currentMarker._latlng.lat,
        lng: currentMarker._latlng.lng
    };
    socket.emit('client-round-end', latlng)
});

function timerTick(countdown) {
    $("#countdown").html(countdown);
    countdown--
    if (countdown == 0) {
        clearInterval(intervalID)
    }
}
