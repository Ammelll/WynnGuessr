const crypto = require('crypto');
const matches = [];
const queue = [];
let room_uuid = crypto.randomUUID();
let mysql = require('mysql');
let connection = mysql.createConnection({
    host: "localhost",
    user: "newuser",
    password: "password",
    database: "wynnguessr"
});
connection.connect(function(err) {
    if (err) throw err;
});
const io = require('socket.io')(3001,{
	cors:true,
	origins: ['http://wynnguessr.com']
});

io.on('connection', (socket) => {
    socket.on('client-queue', (userID) => {
	if(userID == "" || (getMatchByUserID(userID) != null && userID != -1)){
	return;
	}

        queue.push(parseInt(userID));
        socket.join(room_uuid)
        socket.emit("joined-room", room_uuid);
        if (queue.length > 1) {
            var currentPanoramaID = null;
            let one = queue[0];
            let two = queue[1];
            let matchID = null;


            insertPlayers(one, two, function(result) {
                matchID = result.insertId;
                matches.push({
                    room_uuid: room_uuid,
                    matchID: matchID,
                    player_one_id: queue[0],
                    player_two_id: queue[1],
                    player_one_elo_change: null,
                    player_two_elo_change: null,
                    winnerID: null,
                    rounds: [],
                    currentPanoramaID: null,
                    current_term_status_one: false,
                    current_term_status_two: false,
                });

                queue.shift();
                queue.shift();

                let match = matches.at(-1);
          if (match != undefined) {
                    io.to(match.room_uuid).emit('join-game', matchID);
                    getRandomPanoramaID(function(panoramaID) {
                        match.currentPanoramaID = panoramaID;
                    })
                }
                room_uuid = crypto.randomUUID();
            });
        }
    });


    socket.on('client-rejoin-room', (room_uuid, matchID) => {
        let match = getMatchByMatchID(matchID);
        socket.join(room_uuid);
        if (match != null) {
            getPanoramaFileNameFromID(match.currentPanoramaID, function(result) {
                socket.emit("new-round-panorama", result);
            })
        }

    })

    // socket.on('client-panorama-request', (userID) => {
    //     let match = getMatchByUserID(userID);
    //     if (match != undefined) {
    //        getRandomPanoramaID(function(panoramaID) {
    //             match.currentPanoramaID = panoramaID;
    //             getPanoramaFileNameFromID(match.currentPanoramaID, function(result) {
    //                 io.to(match.room_uuid).emit("new-round-panorama", result);
    //             })
    //         })
    //     }
    // })

    socket.on('client-round-end', (latlng) => {
        let room_uuid = latlng.room_uuid;
        let match = getMatchByMatchID(latlng.matchID);
        let userID = latlng.userID;
	console.log(match);
	console.log(isUserInMatch(userID,match));
	console.log(isRoomUUIDValid(room_uuid,match));
        if (isUserInMatch(userID, match) && isRoomUUIDValid(room_uuid, match)) {

            if (isUserPlayerOne(userID, match)) {
                match.current_term_status_one = true;
                pushRound(match, latlng);
                if (!match.current_term_status_two) {
                    io.to(match.room_uuid).emit('round-end-countdown')
                    timeoutID = setTimeout(forceEndRound, 15000, match, {
                        matchID: match.matchID,
                        userID: match.player_one_id,
                        room_uuid: latlng.room_uuid,
                        lat: 50000,
                        lng: 50000
                    })
                }
            }
            if (isUserPlayerTwo(userID, match)) {
                match.current_term_status_two = true;
                pushRound(match, latlng);
                if (!match.current_term_status_one) {
                    io.to(match.room_uuid).emit('round-end-countdown')
                    timeoutID = setTimeout(forceEndRound, 15000, match, {
                        matchID: match.matchID,
                        userID: match.player_two_id,
                        room_uuid: latlng.room_uuid,
                        lat: 50000,
                        lng: 50000
                    })
                }
            }

            if (isMatchDone(match)) {
                clearTimeout(timeoutID);
                setTimeout(() => {
                    getRandomPanoramaID(function(panoramaID) {
                        match.currentPanoramaID = panoramaID;
                        getPanoramaFileNameFromID(match.currentPanoramaID, function(result) {
                            io.to(match.room_uuid).emit("new-round-panorama", result);
                        })
                    })
                    if (match.rounds.length > 9) {
                        matchEnd(match);
                    }
                    match.current_term_status_one = false;
                    match.current_term_status_two = false;
                }, 5000);
                (getLatLngFromPanoramaID(match.currentPanoramaID, function(result) {
                    var results = {
                        player_one_score: getPlayerLastScore(1, match),
                        player_one_id: match.player_one_id,
                        player_two_score: getPlayerLastScore(2, match),
                        player_two_id: match.player_two_id,
			player_one_total_score: getPlayerTotalScore(1,match),
			player_two_total_score:getPlayerTotalScore(2,match),
                        answerLocation: result
                    }
                    io.to(match.room_uuid).emit("round-results", results);
                }))
            }
        }

    });
})
function getPlayerTotalScore(id,match){
    if (id == 1) {
        var playerID = match.player_one_id;
    } else {
        var playerID = match.player_two_id;
    }
var total = 0;
match.rounds.forEach(round =>{
if(playerID == round.userID){
total+=round.score;
}
});
return total;

}

function getPlayerLastScore(id, match) {
if (id == 1) {
        var playerID = match.player_one_id;
    } else {
        var playerID = match.player_two_id;
    }

    if (match.rounds.at(-1).userID == playerID) {
        return match.rounds.at(-1).score
    } else {
        return match.rounds.at(-2).score
    }
}

function isRoomUUIDValid(room_uuid, match) {
    return room_uuid == match.room_uuid;
}

function forceEndRound(match, latlng) {
    pushRound(match, latlng);
                setTimeout(() => {
                    getRandomPanoramaID(function(panoramaID) {
                        match.currentPanoramaID = panoramaID;
                        getPanoramaFileNameFromID(match.currentPanoramaID, function(result) {
                            io.to(match.room_uuid).emit("new-round-panorama", result);
                        })
                    })
                    if (match.rounds.length > 9) {
                        matchEnd(match);
                    }
                    match.current_term_status_one = false;
                    match.current_term_status_two = false;
                }, 5000);
                (getLatLngFromPanoramaID(match.currentPanoramaID, function(result) {
                    var results = {
                        player_one_score: getPlayerLastScore(1, match),
                        player_one_id: match.player_one_id,
                        player_two_score: getPlayerLastScore(2, match),
                        player_two_id: match.player_two_id,
                        player_one_total_score: getPlayerTotalScore(1, match),
                        player_two_total_score: getPlayerTotalScore(2, match),
                        answerLocation: result
                    }
                    io.to(match.room_uuid).emit("round-results", results);
                }))
    if (match.rounds.length > 9) {
        matchEnd(match)
    }
    match.current_term_status_one = false;
    match.current_term_status_two = false;
}


function getWinnerID(match) {
    let p1, p2 = 0
    match.rounds.forEach((round) => {
        if (round.userID == match.player_one_id) {
            p1 += round.score;
        } else {
            p2 += round.score;
        }
    })
    if (p1 > p2) {
        return match.player_one_id;
    } else {
        return match.player_two_id;
    }
}

function getEloChanges(winnerID, match) {
    if (winnerID == match.player_one_id) {
        return [10, -10]
    } else {
        return [-10, 10]
    }
}

function postMatch(match) {
    let winnerID = getWinnerID(match);
    let changes = getEloChanges(winnerID, match);
    let elo_one = changes[0];
    let elo_two = changes[1];
    let matchID = match.matchID;
    postRounds(match.rounds);
    connection.query(`UPDATE matches SET player_one_elo_change = ${elo_one}, player_two_elo_change = ${elo_two}, winnerID = ${winnerID} WHERE matchID = ${matchID}`, function(err, result) {
        if (err) throw err;
    });
    connection.query(`UPDATE users SET elo = elo + ${elo_one} WHERE users.id  = ${match.player_one_id}`, function(err, result) {
        if (err) throw err;

    });
    connection.query(`UPDATE users SET elo = elo + ${elo_two} WHERE users.id = ${match.player_two_id}`, function(err, result) {
        if (err) throw err;

    });
}

function postRounds(rounds) {
    rounds.forEach((round) => {
        let matchID = round.matchID;
        let round_stage = round.round_stage;
        let panoramaID = round.panoramaID;
        let score = round.score;
        let lat = round.lat;
        let lng = round.lng;
        connection.query(`INSERT INTO rounds (matchID, round_stage, panoramaID, score, lat, lng) VALUES (${matchID}, ${round_stage}, ${panoramaID}, ${score}, ${lat}, ${lng})`, function(err, result) {
            if (err) throw err;
        });
    })
}

function matchEnd(match) {
    io.to(match.room_uuid).emit("match-end")
    postMatch(match);
    matches.splice(matches.indexOf(match), 1);
}

function insertPlayers(one, two, callback) {
    connection.query(`INSERT INTO matches(player_one_id, player_two_id) VALUES(${one}, ${two});`, function(err, result) {
        if (err) throw err;
        return callback(result)
    })
}

function getRandomPanoramaID(callback) {
    connection.query(`
                SELECT panoramaID FROM panoramas ORDER BY RAND() LIMIT 1;
                `, function(err, result) {
        if (err) throw err;
        return callback(result[0].panoramaID)
    })
}

function getPanoramaFileNameFromID(panoramaID, callback) {
    connection.query("SELECT `cubemap-filename` FROM panoramas WHERE panoramaID = " + panoramaID, function(err, result) {
        if (err) throw err;
	if(result[0] == null){
		return callback("rymek-east-upper-cubemap.json");
	}
        return callback(result[0]['cubemap-filename']);
    });
}

function getLatLngFromPanoramaID(panoramaID, callback) {
    connection.query("SELECT lat,lng FROM panoramas WHERE panoramaID = " + panoramaID, function(err, result) {
        if (err) throw err;
        return callback([result[0]['lat'], result[0]['lng']]);
    });
}

function isUserInMatch(userID, match) {
if(match != null){
    if (match.player_one_id == userID || match.player_two_id == userID || match.player_one_id == -1 || match.player_two_id == -1) {
	return true
    }
}
    return false;
}

function getMatchByUserID(userID) {
    return matches.find((match) => match.player_one_id == userID || match.player_two_id == userID);
}

function getMatchByMatchID(matchID) {
    return matches.find((match) => match.matchID == matchID);
}

function isUserPlayerOne(userID, match) {
    if (match.player_one_id == userID || match.player_one_id == -1) {
        return true;
    }
    return false;
}

function isUserPlayerTwo(userID, match) {
    if (match.player_two_id == userID || match.player_two_id == -1) {
        return true;
    }
    return false;
}

function isMatchDone(match) {
    if (match.current_term_status_one && match.current_term_status_two) {
        return true;
    }
    return false;
}

function calculateScore(answers, lat, lng) {
    let exactDistance = Math.sqrt(Math.pow(answers[0] - lat, 2) + Math.pow(answers[1] - lng, 2))
    let deadbandedDistance = deadband(exactDistance, 10);
    return Math.round( 2500 - deadbandedDistance);
}

function deadband(value, range) {
    if (value < range) {
        return 0;
    }
    if(2500-value < 0){
	return 2500;
	}
    return value;
}

function pushRound(match, latlng) {
(getLatLngFromPanoramaID(match.currentPanoramaID, function(result) {
	var score =  calculateScore(result, latlng.lat, latlng.lng);
    match.rounds.push({
        matchID: latlng.matchID,
        userID: latlng.userID,
        round_stage: match.rounds.length,
        panoramaID: match.currentPanoramaID,
	score: score,
        lat: latlng.lat,
        lng: latlng.lng
    });
        }))

}
