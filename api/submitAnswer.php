<?php
require_once ('config.php');
ini_set('display_errors', 1);
error_reporting(E_ALL);

$sessionId = $_SESSION["id"] ?? null;

// If the session ID is not available, return an error response
if (!$sessionId) {
    header("HTTP/1.1 401 Unauthorized");
    exit("Unauthorized");
}

$studentId=$_SESSION["studentId"];

$assignmentId =  $_POST['submittedId'];
$submittedAnswer = $_POST['submittedAnswer']; 

$generationIndex = $_SESSION['generationIndex'];
/*
$stmt = $conn->prepare("SELECT submittedAnswer FROM StudentAssignmentLink WHERE studentId = ? AND assignmentId = ?");
$stmt->bind_param('ss', $studentId, $assignmentId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $submittedAnswer2 = $row['submittedAnswer'];
}
if($submittedAnswer2==null){$notNow=false;}
else if($submittedAnswer=$submittedAnswer2){
    $notNow=true;
}else{
    $notNow=false;
}
*/


$stmt = $conn->prepare("UPDATE StudentAssignmentLink SET submittedAnswer = ? WHERE studentId = ? AND assignmentId = ? and generationIndex=?");
$stmt->bind_param('ssss', $submittedAnswer, $studentId, $assignmentId,$generationIndex);
$stmt->execute();

$stmt = $conn->prepare("UPDATE Student SET submittedCount = submittedCount + 1 WHERE id = ?");
$stmt->bind_param('s', $studentId);
$stmt->execute();



$stmt = $conn->prepare("SELECT correctAnswer,maxPoints FROM Assignments WHERE id = ?");
$stmt->bind_param('s',$assignmentId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $correctAnswer = $row['correctAnswer'];
    $maxPoints=$row['maxPoints'];
}



if($correctAnswer==$submittedAnswer){
    $stmt = $conn->prepare("UPDATE StudentAssignmentLink SET achievedPoints = ? WHERE assignmentId = ? and studentId = ? and generationIndex=?");
    $stmt->bind_param('ssss',$maxPoints,$assignmentId, $studentId,$generationIndex);
    $stmt->execute();
}

$stmt = $conn->prepare("SELECT achievedPoints FROM StudentAssignmentLink WHERE studentId = ? AND generationIndex = ?");
$stmt->bind_param('ss', $studentId, $generationIndex);
$stmt->execute();
$result = $stmt->get_result();

$totalAchievedPoints = 0; 

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $achievedPoints = $row['achievedPoints'];

        if ($achievedPoints !== null) {
            $totalAchievedPoints += $achievedPoints;
        }
    }
}

$stmt = $conn->prepare("UPDATE Student SET points=? WHERE id = ?");
$stmt->bind_param('ss', $totalAchievedPoints,$studentId);
$stmt->execute();


?>
