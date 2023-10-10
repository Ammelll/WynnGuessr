<?php
//similar to match-end, updates data in database on round end 
if(isset($_POST)){
    $data = file_get_contents("php://input");
    $scores = json_decode($data, true);
    require_once 'includes/dbh.inc.php';
    $stmt = $pdo->prepare("INSERT INTO rounds (matchID,round_stage,panoramaID_one,panoramaID_two,player_one_score,player_two_score,player_one_lat,player_one_lng,player_two_lat,player_two_lng) VALUES (:matchID,:round_stage,:panoramaID_one,:panoramaID_two,:score1,:score2,:p1_lat,:p1_lng,:p2_lat,:p2_lng);");

        $stmt->bindValue(":score1",$scores['player_one_score']);
        $stmt->bindValue(":score2",$scores['player_two_score']);
        $stmt->bindValue(":matchID",$scores['matchID']);
        $stmt->bindValue(":panoramaID_one",$scores['panoramaID_one']);
        $stmt->bindValue(":panoramaID_two",$scores['panoramaID_two']);
        $stmt->bindValue(":round_stage",$scores['round_stage']);
        $stmt->bindValue(":p1_lat",$scores['p1_lat']);
        $stmt->bindValue(":p1_lng",$scores['p1_lng']);
        $stmt->bindValue(":p2_lat",$scores['p2_lat']);
        $stmt->bindValue(":p2_lng",$scores['p2_lng']);
        $stmt->execute();

        $pdo = null;
        $stmt = null;
    
}