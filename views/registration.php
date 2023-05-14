<?php
require '../api/config.php';

if (!empty($_SESSION["id"])) {
  header("Location: https://site215.webte.fei.stuba.sk/semestralka");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
  <script src="https://code.jquery.com/jquery-3.6.4.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
  <link href="../styles/all.css" rel="stylesheet">

  <title>Registration</title>
</head>

<body>
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
              <a class="nav-link" href="../index.php" data-translate="loginPage"></a>
            </li>
            <li class="nav-item">
              <a class="nav-link active" aria-current="page" href="" data-translate="registration"></a>
            </li>
          </ul>
        </div>
      </div>
    </nav>
  </header>
  <div class="container my-5">
    <h2 class="text-center mb-2" data-translate="registrationPage">Registrujte sa</h2>
    <div class="form-group mb-2">
      <label class="form-label" for="role" data-translate="role">role</label>
      <select id="role" class="form-select">
        <option value="student" data-translate="student"></option>
        <option value="teacher" data-translate="teacher"></option>
      </select>
    </div>
    <div class="form-group mb-2">
      <label for="name" class="form-label">Name</label>
      <input type="text" class="form-control" id="name" placeholder="Enter name" data-required />
      <div class="invalid-feedback ms-2" id="nameError" data-translate="nameError">
        //nameError
      </div>
    </div>

    <div class="form-group mb-2">
      <label for="surname" class="form-label">Surname</label>
      <input type="text" class="form-control" id="surname" placeholder="Enter surname" data-required />
      <div class="invalid-feedback ms-2" id="surnameError" data-translate="surnameError">
        //surnameError
      </div>
    </div>

    <div class="form-group mb-2">
      <label class="form-label" for="email" data-translate="email">Email</label>
      <input type="text" id="email" class="form-control" />
      <div class="invalid-feedback ms-2" id="emailError" data-translate="emailError">
        //emailError
      </div>
    </div>

    <div class="form-group mb-4">
      <label class="form-label" for="password" data-translate="password">Heslo</label>
      <input type="password" id="password" class="form-control" />
      <div class="invalid-feedback ms-2" id="passwordError" data-translate="passwordError">
        //password
      </div>
    </div>

    <button id="register" type="button" class="btn btn-primary w-100" data-translate="register">
      register
    </button>
  </div>

  <div class="modal" tabindex="-1" id="myModal">
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
  <script type="module" src="../languages/languageSwitching.js"></script>
</body>

</html>
<script>
  $('#nameError').hide();
  $('#surnameError').hide();
  $('#emailError').hide();
  $('#passwordError').hide();
  const modal = new bootstrap.Modal('#myModal', {});
  $('#register').click(() => {
    let role = $('#role').val();
    let name = $('#name').val();
    let surname = $('#surname').val();
    let email = $('#email').val();
    let password = $('#password').val();
    let invalidFields = [];
    const regex = /^[A-Za-z]+([ -.]?[A-Za-z]+)*$/;
    if (!regex.test(name)) {
      invalidFields.push("nameError");
    } else {
      $('#nameError').hide();
    }
    if (!regex.test(surname)) {
      invalidFields.push("surnameError");
    } else {
      $('#surnameError').hide();
    }
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
        role: role,
        name: name,
        surname: surname,
        email: email,
        password: password
      }
      console.log(formData);
      $.ajax({
        type: "POST",
        contentType: "application/json",
        url: "/semestralka/api/register.php",
        data: JSON.stringify(formData),
        dataType: "json",
      })
        .done((data) => {
          console.log(data);
          $('.modal-body').html(`<span data-translate="${data.errorCause}"></span>`);
          modal.show();         
        })
        .fail((error) => {
          console.log(error);
        });
    } else {
      invalidFields.forEach(invalidField => {
        $('#' + invalidField).show();
      });
    }
  });
</script>