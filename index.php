<?php
if (isset($_SESSION["id"])) {
  if ($_SESSION["role"] == 'student') {
    header("Location: views/equations.php");
  } else if ($_SESSION["role"] == 'teacher') {
    header("Location: views/teachers.php");
  } 
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
  <link href="./styles/all.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.4.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>

  <title>Login</title>
</head>

<body>
  <div id="wrapper">
    <header>
      <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
          <button class="btn btn-sm btn-secondary languageSwitcher me-1" data-language="sk">Slovenčina</button>
          <button class="btn btn-sm btn-secondary languageSwitcher" data-language="en">English</button>
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
              <li class="nav-item">
                <a class="nav-link active" aria-current="page" href="" data-translate="loginPage"></a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="./views/registration.php" data-translate="registration"></a>
              </li>
              
            </ul>
          </div>
          <a href="print.php" class="btn btn-success btn-sm" role="button" data-translate="Instructions"></a>
        </div>
      </nav>
    </header>


    <div class="container my-5">
      <h2 class="text-center mb-2" data-translate="loginPage">Prihláste sa</h2>
      <div class="form-group mb-2">
        <label class="form-label" for="email" data-translate="email">Email</label>
        <input type="text" id="email" class="form-control form-control-lg" />
        <div class="invalid-feedback ms-2" id="emailError" data-translate="emailError">
          //emailError
        </div>
      </div>

      <div class="form-group mb-3">
        <label class="form-label" for="password" data-translate="password">Heslo</label>
        <input type="password" id="password" class="form-control form-control-lg" />
        <div class="invalid-feedback ms-2" id="passwordError" data-translate="passwordError2">
          //password
        </div>
      </div>

      <button id="login" type="button" class="btn btn-success btn-lg mb-2  w-100" data-translate="login">
        Prihlásiť
      </button>

      <div>
        <span data-translate="noAccountYet">Nemáte účet?</span>
        <a class="ms-1" href="./views/registration.php" data-translate="register">Registrovať</a>
      </div>
    </div>
    <div class="modal text-dark" tabindex="-1" id="myModal">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" data-translate="loginError">loginError</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script type="module" src="./languages/languageSwitching.js"></script>
</body>

</html>
<script>
  $('#emailError').hide();
  $('#passwordError').hide();
  const modal = new bootstrap.Modal('#myModal', {});
  $('#login').click(() => {
    let email = $('#email').val();
    let password = $('#password').val();
    let invalidFields = [];
    const regexEmail = /^\w{2,}([.-]?\w+)@\w+([.-]?\w+)(.\w{2,4})+$/;
    const regexPassword = /^(?=.*[A-Z].*[A-Z])(?=.*\d.*\d.*\d)(?=.*[ -~].*)[ -~]{8,}$/;
    if (!regexEmail.test(email)) {
      invalidFields.push("emailError");
    } else {
      $('#emailError').hide();
    }
    if (!regexPassword.test(password)) {
      invalidFields.push("passwordError");
    } else {
      $('#passwordError').hide();
    }


    if (invalidFields.length === 0) {
      let formData = {
        email: email,
        password: password
      }
      $.ajax({
          type: "POST",
          contentType: "application/json",
          url: "/semestralka/api/login.php",
          data: JSON.stringify(formData),
          dataType: "text",
        })
        .done((data) => {
          console.log(data);
          if (data == 'student') {
            window.location.href = './views/studentHome.php';
          } else if (data == 'teacher') {
            window.location.href = './views/teacher.php';
          }
        })
        .fail((error) => {
          console.log(error);
          $('.modal-body').html(`<span data-translate="${error.responseText}"></span>`);
          modal.show();
        });
    } else {
      invalidFields.forEach(invalidField => {
        $('#' + invalidField).show();
      });
      invalidFields = [];
    }
  });
</script>