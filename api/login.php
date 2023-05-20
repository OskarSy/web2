<?php
require_once ('config.php');
require_once('equationFunctionionality.php');



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

            $_SESSION['id'] = $currentId;
            $_SESSION['email'] = $email;
            $_SESSION['role'] = $role;
            $stmt = $conn->prepare("SELECT id FROM Student WHERE userId = ?");
            $stmt->bind_param('s', $currentId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $studentId = $row['id'];
                $_SESSION['studentId'] = $studentId;
            }           


            if($role == 'student'){
                writeIntoDatabase();               
            }
            http_response_code(200);
            echo $role;
        } 
        else 
        {
            http_response_code(401);
            echo('wrongPassword');
        }
    }
    else 
    {
        http_response_code(404);
        echo('wrongEmail');
    }
    exit();
}?>