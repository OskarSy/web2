<?php
require_once('config.php');
require_once('equationFunctionionality.php');


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $json = file_get_contents('php://input');
  $formData = json_decode($json);
  $name = $formData->name;

  $availableEquations = $_SESSION['availableEquations'];
  $studentId = $_SESSION["studentId"];

  $stmt = $conn->prepare("UPDATE Student SET generatedCount = generatedCount + 1 WHERE id = ?");
  $stmt->bind_param('i', $studentId);
  $stmt->execute();

  $generationIndex = $_SESSION['generationIndex'];

  $generatedEquations = getAllGeneratedEquations($studentId, $generationIndex);
  $usedIds = [];
  if ($generatedEquations) {
      $usedIds = array_column($generatedEquations, 'id');    
  } 
  foreach ($availableEquations as $eq) {
    if ($eq['name'] == $name) {
      if(array_search($eq['id'],  $usedIds) === false){
        $selectedGroup[] = $eq['id'];    
      }
    }
  }
  $randomKey = array_rand($selectedGroup, 1);

  if (count($selectedGroup) >= 0) {
    $stmt = $conn->prepare('INSERT INTO StudentAssignmentLink (assignmentId, studentId, generationIndex) VALUES (?, ?, ?)');
    $stmt->bind_param('sss', $selectedGroup[$randomKey], $studentId, $generationIndex);
    $stmt->execute();
  }
  $generatedEquation = array('id' => $selectedGroup[$randomKey], 'equation' => generateEquation($selectedGroup[$randomKey])[0], 'img' => generateEquation($selectedGroup[$randomKey])[1], 'isSubmitted' => false);
  
  $oldEquations = getAllGeneratedEquations($studentId, $generationIndex);

  $json = json_encode($oldEquations);
  echo $json;
  exit();
}
