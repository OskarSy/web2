<?php
require 'config.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $json = file_get_contents('php://input');
    $formData = json_decode($json);
    $email = $formData->email;
    $password = $formData->password;

    $userResult = mysqli_query($conn, "SELECT * FROM User WHERE email = '$email'");

    if (mysqli_num_rows($userResult) > 0) {
        $user = mysqli_fetch_assoc($userResult);
        if (password_verify($password, $user['password'])) {
            $currentId = $user['id'];
            $role = $user['role'];

            $_SESSION['email'] = $email;
            $_SESSION['role'] = $role;
            if($role == 'student'){
                header("Location: https://site215.webte.fei.stuba.sk/semestralka/views/equations.php");
            }
            elseif($role=='teacher'){
                // header("Location: https://site215.webte.fei.stuba.sk/semestralka/views/equations.php");
            }
        } 
        else 
        {
            echo('wrongPassword');
        }
    }
    else 
    {
        echo('wrongEmail');
    }
    exit();
}?>
