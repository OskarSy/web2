<?php
require 'config.php';

if (!empty($_SESSION["id"])) {
  header("Location: https://site215.webte.fei.stuba.sk/semestralka/views/equations.php");
}

error_reporting(E_ALL);
ini_set('display_errors', 1);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $json = file_get_contents('php://input');
  $formData = json_decode($json);
  $name  = $formData->name;
  $surname  = $formData->surname;
  $email  = $formData->email;
  $password = password_hash($formData->password, PASSWORD_BCRYPT, ['cost' => 13]);
  $role = $formData->role;


  $result = mysqli_query($conn,"SELECT email FROM User WHERE email='$email'");
  if (mysqli_num_rows($result) > 0) {      
    $errorData = ['error' => true, 'errorCause' => 'wrongEmail'];
    echo(json_encode($errorData));
  }
  else{
    $stmt = $conn->prepare('INSERT INTO User (email, password, role) VALUES (?, ?, ?)');
    $stmt->execute([$email, $password, $role]);
    $id = mysqli_insert_id($conn);
 
    if($role=='student'){
      $stmt = $conn->prepare('INSERT INTO Student (name, surname, userId) VALUES (?, ?, ?)');
      $stmt->execute([$name, $surname, $id]);
    }
    header("Location: https://site215.webte.fei.stuba.sk/semestralka/index.php");
  }  
  exit();
}