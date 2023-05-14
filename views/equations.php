<?php
require("../api/equationFunctionionality.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Equations</title>
    <script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.0/MathJax.js?config=TeX-MML-AM_HTMLorMML"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.6.4.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe"
        crossorigin="anonymous"></script>
        <link href="./styles/all.css" rel="stylesheet">

    <style>
        .image-container {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        img {
            max-width: 150px;
            max-height: 150px;
        }
    </style>
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
              <a class="nav-link active" aria-current="page" href="#">Home</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#">Link</a>
            </li>            
          </ul>        
        </div>
      </div>
    </nav>
  </header>   
    <div class="container">
    <?php
          echo $blokovkaLogic;
    ?>
        </div>

    <script type="text/javascript">
        // Function to render the equations using MathJax
        function renderEquations() {
            MathJax.Hub.Queue(["Typeset", MathJax.Hub, document.getElementById('task')]);
            MathJax.Hub.Queue(["Typeset", MathJax.Hub, document.getElementById('solution')]);
        }

        function toggleSolution() {
            var solutionDiv = document.getElementById("solution");
            if (solutionDiv.style.display === "none") {
                solutionDiv.style.display = "block";
            } else {
                solutionDiv.style.display = "none";
            }
        }

        window.addEventListener('load', renderEquations);
    </script>
    <script type="module" src="../languages/languageSwitching.js"></script>
</body>

</html>