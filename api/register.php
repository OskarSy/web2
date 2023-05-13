<?php
require './config/config.php';
require_once './PHPGangsta/GoogleAuthenticator.php';

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
    $errorData = ['error' => true, 'body' => $result->fetch_assoc()];
    echo(json_encode($errorData));
  }
  else{
    $stmt = $conn->prepare('INSERT INTO User (email, password, role) VALUES (?, ?, ?)');
    $stmt->execute([$email, $password, $role]);
    $id = mysqli_insert_id($conn);
    echo(json_encode(['error' => false,'body'=>$codeURL]));
  }  
  exit();
}