<?php
require '../config.php';
require_once '../PHPGangsta/GoogleAuthenticator.php';

if (!empty($_SESSION["id"])) {
    header("Location: https://site221.webte.fei.stuba.sk/index.php");
}
if (isset($_POST["submit"])) {

    $loggedInTime = date('m/d/Y h:i:s a', time());
    $usernameemail = $_POST["usernameemail"];
    $password = $_POST["password"];
    $result = mysqli_query($con, "SELECT * FROM User WHERE username = '$usernameemail' OR email = '$usernameemail'");
    $row = mysqli_fetch_assoc($result);
    if (mysqli_num_rows($result) > 0) {
        if (password_verify($password, $row['password'])) {
            $currentId = $row["id"];
            $re = mysqli_query($con, "SELECT code FROM User WHERE username = '$usernameemail' OR email = '$usernameemail' and id='$currentId'");
            $rw = mysqli_fetch_assoc($re);
            $g2fa = new PHPGangsta_GoogleAuthenticator();
            if ($g2fa->verifyCode($rw["code"], $_POST['2fa'], 2)) {

            $sql = "INSERT INTO user_data (time_when_logged_in,time_when_logged_out,User_id) VALUES ('$loggedInTime','session','$currentId')";
            mysqli_query($con, $sql);
               $_SESSION["login"] = true;
               $_SESSION["id"] = $row["id"];
               $_SESSION["email"]=$row["email"];
               $sql = "UPDATE User
               SET verified='1'
               WHERE id='$currentId'";
                   mysqli_query($con, $sql);
            header("Location: https://site221.webte.fei.stuba.sk/index.php");
            }else {
                $ermessage='Incorrect QR code';
            }
        } else {
            $ermessage='Wrong Password or Username'; 
        }
    } else {
        $ermessage='User Not Registered';
    }
}
if (isset($_POST["getQR"])) {
    $usernameemail = $_POST["usernameemail"];
    $result = mysqli_query($con, "SELECT * FROM User WHERE username = '$usernameemail' OR email = '$usernameemail'");
    $row = mysqli_fetch_assoc($result);
    if (mysqli_num_rows($result) > 0) {
    $currentId = $row["id"];
    $password = $row["password"];
}
if (password_verify($_POST['password'], $password)) {
$re = mysqli_query($con, "SELECT code FROM User WHERE username = '$usernameemail' OR email = '$usernameemail' and id='$currentId'");
    $rw = mysqli_fetch_assoc($re);
    $g2fa = new PHPGangsta_GoogleAuthenticator();
    if (mysqli_num_rows($re) > 0) {
    $codeURL = $g2fa->getQRCodeGoogleUrl('Olympic Games', $rw["code"]);
    $qrcode = $codeURL;
}
}
else{
    $ermessage='User Not Registered / In order to get QR code you need to fill in name and password';
}

}

?>






<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../bootstrap/bootstrap.min.css">
    <script src="../bootstrap/bootstrap.min.js"></script>
    <title>Login</title>
</head>

<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExample08"
                aria-controls="navbarsExample08" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <a class="navbar-brand disabled" href="#">Navbar</a>
            <div class="collapse navbar-collapse justify-content-md-center" id="navbarsExample08">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link"
                            href="../index.php">Slovakia
                            champions</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link"
                            href="tops.php">TOP 10</a>
                    </li>
                </ul>
            </div>
        </nav>
    </header>
    <section class="vh-100">
        <div class="container py-5 h-100">
            <div class="row d-flex align-items-center justify-content-center h-100">
                <div class="col-md-8 col-lg-7 col-xl-6">
                    <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-login-form/draw2.svg"
                        class="img-fluid" alt="Phone image">
                </div>
                <div class="col-md-7 col-lg-5 col-xl-5 offset-xl-1">
                <?php if (!empty($ermessage)) {
                echo "
<div class='alert alert-warning alert-dismissible fade show' role='alert'>
<strong>$ermessage</strong>
</div>
";
              } ?>
                    <form method="post">
                        <!-- Email input -->
                        <div class="form-outline mb-4">
                            <input type="text" name="usernameemail" id="usernameemail"
                                class="form-control form-control-lg"  value="" />
                            <label class="form-label" for="usernameemail">Email address/User name</label>
                        </div>

                        <!-- Password input -->
                        <div class="form-outline mb-4">
                            <input type="password" id="password" name="password" class="form-control form-control-lg"
                                 value="" />
                            <label class="form-label" for="password">Password</label>
                        </div>

                        <div class="form-outline mb-4">
                            <input type="text" id="code" name="2fa" class="form-control form-control-lg" 
                                value="" />
                            <label class="form-label" for="code">Enter code</label>
                        </div>

                        <button type="submit" name="getQR" class="btn btn-primary btn-lg btn-block">Generate QR</button>

                        <?php
        if (isset($qrcode)) {
            // Pokial bol vygenerovany QR kod po uspesnej registracii, zobraz ho.
            $message = '<p>Scan QR code: <br><img src="'.$qrcode.'" alt="qr code for 2fa"></p>';

            echo $message;
        }
        ?>

                        <!-- Submit button -->
                        <button type="submit" name="submit" class="btn btn-primary btn-lg btn-block">Sign in</button>

                        <div class="divider d-flex align-items-center my-4">
                            <p class="text-center fw-bold mx-3 mb-0 text-muted">OR</p>
                        </div>

                        <a class="btn btn-primary btn-lg btn-block" style="background-color: #3b5998"
                            href="<?php echo "https://site221.webte.fei.stuba.sk/php/google.php?action=login"; ?>"
                            role="button">
                            <i class="fab fa-facebook-f me-2"></i>Continue with Google
                        </a>


                    </form>
                </div>
            </div>
        </div>
    </section>

</body>

</html>