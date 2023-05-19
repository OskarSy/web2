<?php
require_once('config.php');
require_once('equationFunctionionality.php');


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $json = file_get_contents('php://input');
  $formData = json_decode($json); 
  $amountToGenerate = $formData->generationCount;

  $availableIds = $_SESSION['availableEquations'];
  

  $studentId = $_SESSION["studentId"];

  $stmt = $conn->prepare("UPDATE Student SET generatedCount = ? where id = ?");
  $stmt->bind_param('ss', $amountToGenerate, $studentId);
  $stmt->execute();



  $stmt = $conn->prepare("SELECT generationIndex FROM StudentAssignmentLink WHERE studentId = ? ORDER BY generationIndex DESC LIMIT 1");
  $stmt->bind_param('s', $studentId);
  $stmt->execute();
  $result = $stmt->get_result();
  if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $currentValue = $row['generationIndex'];
    $generationIndex = $currentValue + 1;
  } else {
    $generationIndex = 0;
  }

  $_SESSION['generationIndex'] = $generationIndex;

  
  if($amountToGenerate==1){
    $randomKeys[] = array_rand($availableIds, $amountToGenerate);
  }
  else{
    $randomKeys = array_rand($availableIds, $amountToGenerate);
  }
 
  if (count($availableIds) >= $amountToGenerate) {
    foreach ($randomKeys as $key) {
      $stmt = $conn->prepare('INSERT INTO StudentAssignmentLink (assignmentId, studentId,generationIndex) VALUES (?, ?, ?)');
      $stmt->bind_param('sss', $availableIds[$key], $studentId, $generationIndex);
      $stmt->execute();
    }
  }
  $generatedEquations = array();
  foreach ($randomKeys as $key) {
    $generatedEquations[] = array('id' => $availableIds[$key], 'equation' => generateEquation($availableIds[$key])[0],'img'=>generateEquation($availableIds[$key])[1], 'isSolved'=>false);
  }
  $_SESSION['currentKeys']=$randomKeys;
  $json = json_encode($generatedEquations);
  echo $json;
  exit();
}
