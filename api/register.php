<?php
require 'config.php';

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




  $result = mysqli_query($conn, "SELECT email FROM User WHERE email='$email'");
  if (mysqli_num_rows($result) > 0) {
    http_response_code(409); 
    echo "userAlreadyExists";
  } else {
    $stmt = $conn->prepare('INSERT INTO User (email, password, role) VALUES (?, ?, ?)');
    $stmt->execute([$email, $password, $role]);
    $id = mysqli_insert_id($conn);

    $_SESSION['id'] = $id;
    $_SESSION['email'] = $email;
    $_SESSION['role'] = $role;

    if ($role == 'student') {
      $stmt = $conn->prepare('INSERT INTO Student (name, surname, userId) VALUES (?, ?, ?)');
      $stmt->execute([$name, $surname, $id]);
    }
    http_response_code(200);
    echo $role;
  }
  exit();
}
