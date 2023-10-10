const socket = io('http://24.130.55.123:3001');
$( document ).ready(function() {

$('#ranked').on("click", ()=>{
socket.emit('client-queue',$('.player-id').html());
$("#ranked").html("RANKED");
$("#ranked").toggleClass("queued");
})

$('#casual').on("click", ()=>{
socket.emit('client-queue',-1);
$("#casual").html("CASUAL");
$("#casual").toggleClass("queued");
})

$('#solo').on("click", ()=>{
socket.emit('client-queue',-1);
socket.emit('client-queue',-1);
$("#solo").html("SOLO");
$("#solo").toggleClass("queued");
})



socket.on('joined-room', (uuid) =>{Cookies.set('room_uuid', uuid)})
socket.on('join-game',(matchID)=>{Cookies.set('match_id',matchID)
window.location = "game.php";
})
})







