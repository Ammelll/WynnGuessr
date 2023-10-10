<?php

if(isset($_POST)){
    $data = file_get_contents("php://input");
    $match = json_decode($data, true);
    $matchID = $match['matchID'];
    require_once 'includes/dbh.inc.php';

    $stmt = $pdo->prepare("SELECT  matches.player_one_id,matches.player_two_id,player_one_score,player_two_score FROM rounds JOIN matches ON matches.matchID = :matchID WHERE rounds.matchID = :matchID;");
    $stmt->bindparam(":matchID",$matchID);
    $stmt->execute();
    $scores = $stmt->fetchAll();

      
    $stmt = $pdo->prepare("SELECT id,elo FROM `users` JOIN matches ON matches.player_one_id = users.id OR matches.player_two_id = users.id WHERE matches.matchID = :matchID;");
    $stmt->bindparam(":matchID",$matchID);
    $stmt->execute();
    $elos = $stmt->fetchAll();
    $player_one_id = $scores[0]["player_one_id"];
    $player_two_id = $scores[0]["player_two_id"];
    $player_one_total_score = 0;
    $player_two_total_score = 0;
    //counts score to determine higher (in ternary)
    foreach($scores as $roundScore){
        $player_one_total_score+=$roundScore['player_one_score'];
        $player_two_total_score+=$roundScore['player_two_score'];
    }
    //solo game check 
    if(count($elos) != 1){
        foreach($elos as $elo){
            if($elo['id'] == $player_one_id){
                $player_one_elo = $elo["elo"];
            } else{
                $player_two_elo =  $elo["elo"];
            }
        }
    } else{
        $player_one_elo = $elos[0]["elo"];
        $player_two_elo =  $elos[0]["elo"];
    }
    //look up ternary statements if this confuses you!
    $winnerID = $player_one_total_score > $player_two_total_score ? $player_one_id : $player_two_id;
    //elo system implentation (glicko modified no K value)
    if($winnerID == $player_one_id){
        $expected_one = 1/(pow(10,((($player_two_elo-$player_one_elo)/400)))+1);
        $expected_two = 1/(pow(10,((($player_one_elo-$player_two_elo)/400)))+1);
        $player_one_elo_change = floor(20 * (1-$expected_one));
        $player_two_elo_change = floor(20 * (0-$expected_two));
        $player_one_elo+=$player_one_elo_change;
        $player_two_elo+=$player_two_elo_change;
    } else{
        $expected_one = 1/(pow(10,((($player_two_elo-$player_one_elo)/400)))+1);
        $expected_two = 1/(pow(10,((($player_one_elo-$player_two_elo)/400)))+1);
        $player_one_elo_change = floor(20 * (0-$expected_one));
        $player_two_elo_change = floor(20 * (1-$expected_two));
        $player_one_elo+=$player_one_elo_change;
        $player_two_elo+=$player_two_elo_change;
    }
}


    //inputs final updated elos and winner based on previous
    $stmt = $pdo->prepare("UPDATE matches SET player_one_elo_change = :p1, player_two_elo_change = :p2, winnerID = :winnerID WHERE matchID = :matchID");
    $stmt->bindValue(":p1",$player_one_elo_change);
    $stmt->bindValue(":p2",$player_two_elo_change);
    $stmt->bindValue(":winnerID",$winnerID);
    $stmt->bindValue(":matchID",$matchID);
    $stmt->execute();

    $stmt = $pdo->prepare("UPDATE users SET elo = :elo WHERE id =:p1");
    $stmt->bindValue(":elo",$player_one_elo);
    $stmt->bindValue(":p1", $player_one_id);
    $stmt->execute();

    $stmt = $pdo->prepare("UPDATE users SET elo = :elo WHERE id =:p2");
    $stmt->bindValue(":elo",$player_two_elo);
    $stmt->bindValue(":p2", $player_two_id);
    $stmt->execute();
    if($player_one_id == $player_two_id){
        $stmt = $pdo->prepare("UPDATE users SET elo = :elo WHERE id =:p1");
        $stmt->bindValue(":elo",($player_two_elo+$player_one_elo)/2);
        $stmt->bindValue(":p1", $player_one_id);
        $stmt->execute();
    }
    

    $pdo = null;
    $stmt = null;
