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

$('#duel').on("click",()=>{
	var special = window.crypto.getRandomValues(new Uint32Array(1));
	copyText("http://wynnguessr.com/duel.php?gameKey=" + special);
	$("#duel").html("COPIED");
});

socket.on('joined-room', (uuid) =>{Cookies.set('room_uuid', uuid)})
socket.on('join-game',(matchID)=>{Cookies.set('match_id',matchID)
window.location = "game.php";
})
})
  function copyText(input) {
      var $temp = $("<input>");
      $("body").append($temp);
      $temp.val(input).select();
      document.execCommand("copy");
      $temp.remove();
    }

