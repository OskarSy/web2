<?php
require_once ('config.php');

ini_set('display_errors', 1);
error_reporting(E_ALL);


// Check if the session is active and retrieve the session ID
$sessionId = $_SESSION["id"] ?? null;

// If the session ID is not available, return an error response
if (!$sessionId) {
    header("HTTP/1.1 401 Unauthorized");
    exit("Unauthorized");
}
$amountGenerated=$_POST['inputValue'];

$result = $conn->query("SELECT a.id
                        FROM Assignments a
                        INNER JOIN AssignmentGroup ag ON a.groupId = ag.id
                        WHERE ag.canBeUsed = '1'");

$ids = array(); // Initialize an empty array to store the IDs


if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $ids[] = $row['id'];
    }
}

$studentId=$_SESSION["studentId"];

$stmt = $conn->prepare("UPDATE Student SET generatedCount = ? where id = '$studentId'");
$stmt->bind_param('s',$amountGenerated );
$stmt->execute();



$result = $conn->query("SELECT userIndex FROM StudentAssignmentLink WHERE studentId = '$studentId' ORDER BY userIndex DESC LIMIT 1");

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $currentValue = $row['userIndex'];
    $userIndex = $currentValue + 1;
} else {
    $userIndex = 0;
}



$_SESSION['userIndex'] = $userIndex;
$randomIds = array();
if (count($ids) >= $amountGenerated) {
    $randomKeys = array_rand($ids, $amountGenerated);
    foreach ($randomKeys as $key) {
        $stmt = $conn->prepare('INSERT INTO StudentAssignmentLink (assignmentId, studentId,userIndex) VALUES (?, ?, ?)');
        $stmt->bind_param('sss', $ids[$key], $studentId, $userIndex);
        $stmt->execute();
    }
}


?>