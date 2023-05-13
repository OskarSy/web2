<?php
require './config/config.php';

if (!empty($_SESSION["id"])) {
    header("Location: https://site215.webte.fei.stuba.sk/semestralka");
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $json = file_get_contents('php://input');
    $formData = json_decode($json);

    $login = $formData->login;
    $password = $formData->password;

    $personResult = mysqli_query($conn, "SELECT * FROM User WHERE email = '$login'");

    if (mysqli_num_rows($personResult) > 0) {
        $person = mysqli_fetch_assoc($personResult);
        if (password_verify($password, $person['password'])) {
            $currentId = $person['id'];

            echo(json_encode(['error' => false,'body'=>["id"=>$currentId,"email"=>$person['email']]]));
        } 
        else 
        {
            echo(json_encode(['error' => true,'body'=>'Wrong password or username']));
        }
    } 
    else 
    {
        echo(json_encode(['error' => true,'body'=>'User is not registered']));
    }
    exit();
}