

$( document ).ready(function() {
    //gets panorama data from database -> php -> js
    function getGamePanorama(){
        let fn = $(`.panorama-data .cubemap-filename${round}`).html();
        let lat = $(`.panorama-data  .lat${round}`).html();
        let lng = $(`.panorama-data .lng${round}`).html();
        return new Panorama(fn,L.marker(L.latLng(lat,lng)));
    }
    //defaults values
    let round = 0;
    let finished = false;
    let currentMarker = null;
    let panorama = getGamePanorama();
    let answerLocation = panorama.getMarker();
    let turn = 1;
    let playerOneScore = 0;
    let playerTwoScore = 0;
    let panoramaID_one = -1;
    let panoramaID_two = -1;
    let p1_lat =0;
    let p1_lng =0;
    let p2_lat = 0;
    let p2_lng = 0;
    let matchID = $(".game-data").html();
    newTurn();
    $("#submit").attr("disabled",true);

    //on submit check to see if round data needs to be updated, stores playerdata for roundstage
    $("#submit").on("click",() =>{
        finished = true;
        $("#continue").toggleClass("hidden");
        if(round % 2 == 0){
            playerOneScore = getScore(currentMarker._latlng.lat, currentMarker._latlng.lng);
            panoramaID_one = $(`.panorama-data .panoramaID${round}`).html();
            p1_lat = currentMarker._latlng.lat;
            p1_lng = currentMarker._latlng.lng;

        } else{
            playerTwoScore = getScore(currentMarker._latlng.lat, currentMarker._latlng.lng);
            panoramaID_two = $(`.panorama-data .panoramaID${round}`).html();
            p2_lat = currentMarker._latlng.lat;
            p2_lng = currentMarker._latlng.lng;
            postRound(playerOneScore,playerTwoScore);

        }
        //transition screen (makes panorama no longer viewable)
        var tscreen = $("<div id='transition-screen'></div>")
        $("#submit").attr("disabled",true)
        $("body").append(tscreen);
        $("#map").css("opacity",1.0);
        $("#map").css("z-index",2);
        //adds correct location to map
        answerLocation.addTo(map);
    })
    //on continue check if match is over, if so post round data, else reset default values to next
    $("#continue").on("click", ()=>{
        round++;
        if($("#continue").html() == "Finish"){
            window.location = "home.php";
        }
        if(round > 9){
            postMatch();
            $("#continue").html("Finish");

        } else{
            finished = false;
            answerLocation.removeFrom(map);
            currentMarker.removeFrom(map);
            panorama = getGamePanorama();
            answerLocation = panorama.getMarker();
            $("#transition-screen").remove();
            $("#continue").toggleClass("hidden");
            newTurn();
            $(".panorama-container").html(`<iframe id="panorama" allowfullscreen style="border-style:none;" src=${panorama.getPath()}></iframe>`);
        }
    })
    //set up leaflet map
    $("body").append(`<div class="map-container">
    <div id='map'></div>
    <button id="submit"disabled="true">Submit</button>
    </div>`);
    var map = L.map('map', {
        crs: L.CRS.Simple
        });
        //set scale
        var bounds = [[0,0], [648.5,409.1]];
        //set image overlay
        var image = L.imageOverlay("imgs/wynnmap.png", bounds).addTo(map);
        map.fitBounds(bounds);
        //hide attribution (corner Leaflet symbol)
        $('.leaflet-control-attribution').hide();
        //on click update marker if game is ongoing. If not first click, remove previous guess marker
        map.on('click',function (ev){   

            if(!(currentMarker == null) && !finished){
                currentMarker.removeFrom(map);
            }
            if(!finished){
                updateMarker();
                currentMarker.addTo(map);
                $("#submit").attr("disabled",false);
            }



            function updateMarker(){
                currentMarker = L.marker(L.latLng(ev.latlng.lat,ev.latlng.lng));
            }
        });
        //create iframe (panorama) check pannellum.org for documentation
        $(".panorama-container").append(`<iframe id="panorama" allowfullscreen style="border-style:none;" src=${panorama.getPath()}></iframe>`);
    //updates values on new turn
    function newTurn(){
        let currentPlayer = $(`.username-${turn}`).html();
        $("h1").html("Current Player: " + currentPlayer);
        if(turn == 1){
            turn = 2;
        } else{
            turn = 1;
        }
    }
    //calculates final score
    function getScore(x,y){
        let xDiff = Math.abs(answerLocation._latlng.lat-x);
        let yDiff = Math.abs(answerLocation._latlng.lng-y);
        let distance = 15*Math.sqrt(xDiff*xDiff + yDiff*yDiff);
        if(distance > 5000){
            distance = 5000;
        }
         return 5000-Math.floor(distance);
    }
    //posts round data via fetch to round-end.php
    function postRound(p1,p2){
        if(round % 2 == 0){
            round_stage = round/2 + 1;
        } else{
            round_stage = (round+1)/2;
        }
        /*
        0,1 (1)
        2,3 (2)
        4,5 (3)
        6,7 (4)
        8,9(5)
        */
        scores = {
            "player_one_score":p1,
            "player_two_score":p2,
            "matchID":matchID,
            "panoramaID_one":panoramaID_one,
            "panoramaID_two":panoramaID_two,
            "round_stage":round_stage,
            "p1_lat":p1_lat,
            "p1_lng":p1_lng,
            "p2_lat":p2_lat,
            "p2_lng":p2_lng
        }
        fetch("round-end.php",{
            "method": "POST",
            "headers":{
                "Content-Type": " application/json; charset=utf-8"
            },
            "body":JSON.stringify(scores)
        }).then(function(response){
            return response.text();
        }).then(function(data){
        })
    }
    //similar to post-round for matches
    function postMatch(){
        matchIDObj = {
            "matchID":matchID
        }
        fetch("match-end.php",{
            "method": "POST",
            "headers":{
                "Content-Type": " application/json; charset=utf-8"
            },
            "body":JSON.stringify(matchIDObj)
         }).then(function(response){
            return response.text();
        }).then(function(data){
        })
    }

});
