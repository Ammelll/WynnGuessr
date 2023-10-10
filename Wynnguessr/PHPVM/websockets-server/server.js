
const crypto = require('crypto');
const matches = [];
const queue = []
let room_uuid = crypto.randomUUID();
let mysql = require('mysql');
let connection = mysql.createConnection({
    host: "35.232.166.198",
    user: "root",
    password: "pr0toc@\\76h;;",
    database:"wynnguessr"
  });
connection.connect(function(err) {
if (err) throw err;
}); 

const io = require('socket.io')(3001,{
    cors:{
        origin: ['localhost:3001']
    }
});


io.on('connection', (socket) =>{
console.log('bs');
    socket.on('client-queue', (userID)=>{
        console.log(1);
        queue.push(parseInt(userID));
        socket.join(room_uuid)
        socket.emit("joined-room", room_uuid);
        if(queue.length > 1){
            var currentPanoramaID = null;
            let one = queue[0];
            let two = queue[1];
            let matchID = null;


        insertPlayers(one,two,function(result){
            matchID = result.insertId;
            getRandomPanoramaID(function(result){
                currentPanoramaID = result
                matches.push({
                    room_uuid:room_uuid,
                    matchID:matchID,
                    player_one_id:queue[0],
                    player_two_id:queue[1],
                    player_one_elo_change:null,
                    player_two_elo_change:null,
                    winnerID:null,
                    rounds: [],
                    currentPanoramaID:currentPanoramaID
                });
                queue.shift();
                queue.shift();
                let match = matches.filter(match => {
                    return match.player_one_id = userID;
                })[0];
                if(match != undefined){
                    io.to(match.room_uuid).emit('join-game');
                }
                room_uuid = crypto.randomUUID();
            })
        });
        }
    });


    socket.on('client-rejoin-room', room_uuid =>{
        socket.join(room_uuid);
    })

    socket.on('client-panorama-request', async (userID)=>{
        let match = matches.filter(match => {
            return match.player_one_id = userID;
        })[0];
        if(match != undefined){
            let panoramaID = match.currentPanoramaID;            
            getPanoramaFileNameFromID(panoramaID,function(result){
                io.to(match.room_uuid).emit("new-round-panorama",result);
            })
        }
    })

    socket.on('client-round-end', (latlng)=>{
        let match = matches.filter(match => {
            return match.matchID = latlng.matchID;
        })[0];
        match.rounds.push({
            matchID:latlng.matchID,
            round_stage:match.rounds.length,
            panoramaID:match.currentPanoramaID,
            score:(getLatLngFromPanoramaID(match.currentPanoramaID, function(result){
                console.log(result)
                calculateScore(result,latlng.lat,latlng.lng);
            }),latlng.lat,latlng.lng),
            lat:latlng.lat,
            lng:latlng.lng
        });
    });
})
function insertPlayers(one,two,callback){
    connection.query(`INSERT INTO matches (player_one_id,player_two_id) VALUES (${one},${two});`, function (err, result) {
        if (err) throw err;
        return callback(result);
        });
}

function getRandomPanoramaID(callback){
    
    connection.query(`SELECT panoramaID FROM panoramas ORDER BY RAND() LIMIT 1;`, function (err, result) {
        if (err) throw err;
        return callback(result[0].panoramaID);
        });
}
function getPanoramaFileNameFromID(panoramaID,callback){
    connection.query("SELECT `cubemap-filename` FROM panoramas WHERE panoramaID = " + panoramaID, function (err, result) {
        if (err) throw err;
        return callback(result[0]['cubemap-filename']);
        });
}
function getLatLngFromPanoramaID(panoramaID, callback){
    connection.query("SELECT lat,lng FROM panoramas WHERE panoramaID = " + panoramaID, function (err, result) {
        if (err) throw err;
        return callback([result[0]['lat'],result[0]['lng']]);
        });}

function calculateScore(answers,lat,lng){
    console.log(answers);
    return (5000);
}
