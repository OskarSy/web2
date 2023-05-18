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

$stmt = $conn->prepare("SELECT submittedAnswer FROM StudentAssignmentLink WHERE studentId = ? AND assignmentId = ?");
$stmt->bind_param('ss', $studentId, $assignmentId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $submittedAnswerCheck = $row['submittedAnswer'];
}

$stmt = $conn->prepare("UPDATE StudentAssignmentLink SET submittedAnswer = ? WHERE studentId = ? AND assignmentId = ?");
$stmt->bind_param('sss', $submittedAnswer, $studentId, $assignmentId);
$stmt->execute();

if($submittedAnswerCheck==null){
$stmt = $conn->prepare("UPDATE Student SET submittedCount = submittedCount + 1 WHERE id = ?");
$stmt->bind_param('s', $studentId);
$stmt->execute();
}
?>
