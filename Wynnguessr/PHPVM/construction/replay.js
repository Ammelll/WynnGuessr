$( document ).ready(function() {
let turn = "playerOne";
let stage = 1;
//gets panorama data based on replay round
function getGamePanorama(){
    let fn = $(`.${stage}.${turn}.filename`).html();
    let lat = $(`.${stage}.${turn}.lat`).html();
    let lng = $(`.${stage}.${turn}.lng`).html();
    return new Panorama(fn,L.marker(L.latLng(lat,lng)));
}
//displays leaflet map
$("body").append(`<div class="map-container">
<div id='map'></div>
</div>`);
var map = L.map('map', {
    crs: L.CRS.Simple
    });
    var bounds = [[0,0], [648.5,409.1]];
    var image = L.imageOverlay("imgs/wynnmap.png", bounds).addTo(map);
    map.fitBounds(bounds);
    $('.leaflet-control-attribution').hide();


let panorama = getGamePanorama();
let guessLocation = panorama.getMarker();
let answerLng = $(`.${stage}.${turn}.answer-lng`).html();
let answerLat = $(`.${stage}.${turn}.answer-lat`).html();
let answerLocation = L.marker(L.latLng(answerLat,answerLng));
//adds popups to map to display correct and estimated guess
answerLocation.addTo(map).bindPopup("<b>Answer Location</b>", {autoClose:false}).openPopup();
guessLocation.addTo(map).bindPopup("<b>Guess Location</b>", {autoClose:false}).openPopup();
$(".panorama-container").html(`<iframe id="panorama" allowfullscreen style="border-style:none;" src=${panorama.getPath()}></iframe>`);
$("#continue").on("click", ()=>{
    if(turn == "playerOne"){
        turn = "playerTwo";
    } else{
        stage++;
        if(stage == 5){
            window.location = "profile.php";
        }
        turn = "playerOne";
    }
    //renews values for updated round
    panorama = getGamePanorama();
    answerLocation.removeFrom(map);
    guessLocation.removeFrom(map);
    guessLocation = panorama.getMarker();
    answerLat = $(`.${stage}.${turn}.answer-lat`).html();
    answerLng = $(`.${stage}.${turn}.answer-lng`).html();
    answerLocation = L.marker(L.latLng(answerLat,answerLng));
    answerLocation.addTo(map).bindPopup("<b>Answer Location</b>", {autoClose:false}).openPopup();
    guessLocation.addTo(map).bindPopup("<b>Guess Location</b>", {autoClose:false}).openPopup();
    $(".panorama-container").html(`<iframe id="panorama" allowfullscreen style="border-style:none;" src=${panorama.getPath()}></iframe>`);
})
});