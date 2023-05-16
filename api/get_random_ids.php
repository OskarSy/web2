<?php
require 'config.php';


// Check if the session is active and retrieve the session ID
session_start();
$sessionId = $_SESSION["id"] ?? null;

// If the session ID is not available, return an error response
if (!$sessionId) {
    header("HTTP/1.1 401 Unauthorized");
    exit("Unauthorized");
}

$result = $conn->query("SELECT id FROM Assignments");

$ids = array(); // Initialize an empty array to store the IDs

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $ids[] = $row['id'];
    }
}

$randomIds = array();
if (count($ids) >= 5) {
    $randomKeys = array_rand($ids, 5);
    foreach ($randomKeys as $key) {
        $stmt = $conn->prepare('INSERT INTO StudentAssignmentLink (assignmentId, studentId) VALUES (?, ?)');
        $stmt->bind_param('ss', $ids[$key], $sessionId);
        $stmt->execute();
    }
}


?>