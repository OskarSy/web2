<?php
require '../config.php';

require_once '../PHPGangsta/GoogleAuthenticator.php';



if (!empty($_SESSION["id"])) {
  header("Location: https://site221.webte.fei.stuba.sk/index.php");
}
if (isset($_POST["submit"])) {
  $name = $_POST["name"];
  $surname = $_POST["surname"];
  $username = $_POST["username"];
  $email = $_POST["email"];
  $password = $_POST["password"];
  $confirmpassword = $_POST["confirmpassword"];
  $duplicate = mysqli_query($con, "SELECT * FROM User WHERE username = '$username' OR email = '$email'");





  if (mysqli_num_rows($duplicate) > 0) {
    echo
      "<script> alert('Username or Email Has Already Taken'); </script>";
  } else {
    if ($password == $confirmpassword) {
      $options = [
        'cost' => 12,
      ];
      $g2fa = new PHPGangsta_GoogleAuthenticator();
      $user_secret = $g2fa->createSecret();
      $codeURL = $g2fa->getQRCodeGoogleUrl('Olympic Games', $user_secret);
      $qrcode = $codeURL;
      $password = password_hash($password, PASSWORD_BCRYPT, $options);
      $verified="0";
      $sql = "INSERT INTO User (name,surname,username,password,email,role,method,code,verified) VALUES ('$name','$surname','$username','$password','$email','User','Register','$user_secret','$verified')";
      mysqli_query($con, $sql);


    /*  echo
        "<script> alert('Registration Successful'); </script>";
      header("Location: https://site221.webte.fei.stuba.sk/index.php");*/
    } else {
      echo
        "<script> alert('Password Does Not Match'); </script>";
    }
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
  <title>Register</title>
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
            <a class="nav-link" href="../index.php">Slovakia
              champions</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="tops.php">TOP 10</a>
          </li>
        </ul>
      </div>
    </nav>
  </header>


  <section class="vh-100 bg-image"
    style="background-image: url('https://mdbcdn.b-cdn.net/img/Photos/new-templates/search-box/img4.webp');">
    <div class="mask d-flex align-items-center h-100 gradient-custom-3">
      <div class="container h-100">
        <div class="row d-flex justify-content-center align-items-center h-100">
          <div class="col-12 col-md-9 col-lg-7 col-xl-6">
            <div class="card" style="border-radius: 15px;">
              <div class="card-body p-5">
                <h2 class="text-uppercase text-center mb-5">Create an account</h2>

                <form method="post">
                <?php
        if (!isset($qrcode)) { ?>
                  <div class="form-outline mb-4">
                    <input type="text" id="name" name="name" class="form-control form-control-lg" required value="" />
                    <label class="form-label" for="name">Your Name</label>
                  </div>

                  <div class="form-outline mb-4">
                    <input type="text" id="surname" name="surname" class="form-control form-control-lg" required
                      value="" />
                    <label class="form-label" for="surname">Your Surname</label>
                  </div>

                  <div class="form-outline mb-4">
                    <input type="text" name="username" id="username" class="form-control form-control-lg" required
                      value="" />
                    <label class="form-label" for="username">Your Username</label>
                  </div>

                  <div class="form-outline mb-4">
                    <input type="email" id="email" name="email" class="form-control form-control-lg" required
                      value="" />
                    <label class="form-label" for="email">Your Email</label>
                  </div>

                  <div class="form-outline mb-4">
                    <input type="password" name="password" id="password" class="form-control form-control-lg" required
                      value="" />
                    <label class="form-label" for="password">Password</label>
                  </div>

                  <div class="form-outline mb-4">
                    <input type="password" name="confirmpassword" id="confirmpassword"
                      class="form-control form-control-lg" required value="" />
                    <label class="form-label" for="confirmpassword">Repeat your password</label>
                  </div>


                  <div class="d-flex justify-content-center">
                    <button type="submit" class="btn btn-success btn-block btn-lg gradient-custom-4 text-body"
                      name="submit">Register</button>
                  </div>

                  <p class="text-center text-muted mt-5 mb-0">Have already an account? <a
                      href="https://site221.webte.fei.stuba.sk/php/login.php" class="fw-bold text-body"><u>Login
                        here</u></a></p>
                        <?php     }
                      ?>
                        <?php
        if (isset($qrcode)) {
            // Pokial bol vygenerovany QR kod po uspesnej registracii, zobraz ho.
            $message = '<p>Scan QR code: <br><img src="'.$qrcode.'" alt="qr code for 2fa"></p>';

            echo $message;
            echo '<p class="text-center text-muted mt-5 mb-0"><a
            href="https://site221.webte.fei.stuba.sk/php/login.php" class="fw-bold text-body"><u>Login
              here</u></a></p>';
        }
        ?>

                </form>

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>




</body>

</html>