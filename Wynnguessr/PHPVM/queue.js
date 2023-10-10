const socket = io('http://24.130.55.123:3001');
$( document ).ready(function() {
    $('#queue').on("click", ()=>{
	console.log(socket);
        socket.emit('client-queue',$('.player-id').html());
	$("#queue").html("IN QUEUE");
  })
    socket.on('joined-room', uuid =>{
        Cookies.set('room_uuid', uuid)
	console.log('joined-room');
    })
    socket.on('join-game',(matchID)=>{
	Cookies.set('match_id',matchID);
        window.location = "game.php";
    })
})

