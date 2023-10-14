const socket = io('http://24.130.55.123:3001');
$( document ).ready(function() {
let key = $("#key").html();
let userID = $('.player-id').html();
socket.emit("join-duel",{
gkey: key,
uid: userID
});
socket.on('joined-room', (uuid) =>{Cookies.set('room_uuid', uuid)})
socket.on('join-game',(matchID)=>{Cookies.set('match_id',matchID)
console.log(matchID);
window.location = "game.php";
})

});
