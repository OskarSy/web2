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

$assignmentId = $_SESSION['currentEquation'];
$submittedAnswer = $_POST['submittedAnswer']; 

$stmt = $conn->prepare("UPDATE StudentAssignmentLink SET submittedAnswer = ? WHERE studentId = ? AND assignmentId = ?");
$stmt->bind_param('sss', $submittedAnswer, $studentId, $assignmentId);
$stmt->execute();

$response = [
    'assignmentId' => $assignmentId,
    'submittedAnswer' => $submittedAnswer,
    'studentId' => $studentId
];

// Send the response as JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
