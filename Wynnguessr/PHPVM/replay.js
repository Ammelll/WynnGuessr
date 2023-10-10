$(document).ready(function() {
    let stage = 0;

    function getGamePanorama() {
        let fn = $(`.${stage}.filename`).html();
        return new Panorama(fn);
    }
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
];    var image = L.imageOverlay("imgs/wynnmap.png", bounds).addTo(map);
    map.fitBounds(bounds);
    $('.leaflet-control-attribution').hide();


    let panorama = getGamePanorama();
    let guessLat = $(`.${stage}.lat`).html();
    let guessLng = $(`.${stage}.lng`).html();
    let guessLocation = L.marker(L.latLng(guessLat,guessLng))
    let answerLng = $(`.${stage}.answer-lng`).html();
    let answerLat = $(`.${stage}.answer-lat`).html();
    let answerLocation = L.marker(L.latLng(answerLat, answerLng));
    answerLocation.addTo(map).bindPopup("<b>Answer Location</b>", {
        autoClose: false
    }).openPopup();
console.log(guessLat,guessLng);
    //guessLocation.addTo(map).bindPopup("<b>Guess Location</b>", {autoClose:false}).openPopup();
    $(".panorama-container").html(`<iframe id="panorama" allowfullscreen style="border-style:none;" src=${panorama.getPath()}></iframe>`);
    $("#continue").on("click", () => {
        stage++;
        if (stage == 10) {
            window.location = "profile.php";
        }
    panorama = getGamePanorama();
    answerLocation.removeFrom(map);
    guessLocation.removeFrom(map);
     guessLat = $(`.${stage}.lat`).html();
    guessLng = $(`.${stage}.lng`).html();
	guessLocation = L.marker(L.latLng(guessLat,guessLng))
    answerLat = $(`.${stage}.answer-lat`).html();
    answerLng = $(`.${stage}.answer-lng`).html();
    answerLocation = L.marker(L.latLng(answerLat, answerLng));
    answerLocation.addTo(map).bindPopup("<b>Answer Location</b>", {
        autoClose: false
    }).openPopup();
    guessLocation.addTo(map).bindPopup("<b>Guess Location</b>", {
        autoClose: false
    }).openPopup();
    $(".panorama-container").html(`<iframe id="panorama" allowfullscreen style="border-style:none;" src=${panorama.getPath()}></iframe>`);
    })
 panorama = getGamePanorama();
    answerLocation.removeFrom(map);
    guessLocation.removeFrom(map);
     guessLat = $(`.${stage}.lat`).html();
    guessLng = $(`.${stage}.lng`).html();
        guessLocation = L.marker(L.latLng(guessLat,guessLng))
    answerLat = $(`.${stage}.answer-lat`).html();
    answerLng = $(`.${stage}.answer-lng`).html();
    answerLocation = L.marker(L.latLng(answerLat, answerLng));
    answerLocation.addTo(map).bindPopup("<b>Answer Location</b>", {
        autoClose: false
    }).openPopup();
    guessLocation.addTo(map).bindPopup("<b>Guess Location</b>", {
        autoClose: false
    }).openPopup();
    $(".panorama-container").html(`<iframe id="panorama" allowfullscreen style="border-style:none;" src=${panorama.getPath()}></iframe>`);

});
