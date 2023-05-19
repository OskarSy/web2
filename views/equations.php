<?php
require_once "../api/config.php";
require_once("../api/equationFunctionionality.php");

if (empty($_SESSION["id"])) {
    header("Location: ../index.php");
}
if (empty($_GET['equationId'])) {
    header("Location: ../views/studentHome.php");
}
$generationIndex = $_SESSION['generationIndex'];
$studentId = $_SESSION['studentId'];
$result = $conn->query("SELECT id,submittedAnswer FROM StudentAssignmentLink WHERE generationIndex='$generationIndex' and studentId='$studentId'");

$sidebarItems = array();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Equations</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/mathquill/0.10.1/mathquill.min.css">
    <script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.0/MathJax.js?config=TeX-MML-AM_HTMLorMML"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.6.4.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe"
        crossorigin="anonymous"></script>
    <link href="../styles/all.css" rel="stylesheet">

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

        #mathquill-editor {
            width: 400px;
            height: 200px;
        }
    </style>
</head>

<body id="wrapper">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mathquill/0.10.1/mathquill.min.js"></script>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">
                    <?php echo $_SESSION['email'] ?>
                </a>
                <button class="btn btn-sm btn-secondary languageSwitcher me-1" data-language="sk">Slovenčina</button>
                <button class="btn btn-sm btn-secondary languageSwitcher" data-language="en">English</button>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item float-right">
                            <a class="nav-link" href="../views/studentHome.php">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../api/logout.php">Logout</a>
                        </li>
                    </ul>

                </div>
            </div>
        </nav>
    </header><br>
    <div class="container-fluid">
        <?php
        echo generateEquation($_GET['equationId'])[0];
        echo generateEquation($_GET['equationId'])[1];
        ?><br>
        <div id="mathquill-editor"></div>
        <button id="submit-btn" data-translate="submit" class="btn btn-primary">Submit</button>
    </div>
    <script type="text/javascript">
        $(document).ready(function () {
            var MQ = MathQuill.getInterface(2);
            var mathField = MQ.MathField(document.getElementById('mathquill-editor'), {
                handlers: {
                    edit: function () {

                    }
                },
                restrictMismatchedBrackets: true
            });
            $('#submit-btn').on('click', function () {
                var userAnswer = mathField.latex();
                var equationId = <?php echo $_GET['equationId']; ?>; // Corrected line
                $.ajax({
                    url: '../api/submitAnswer.php',
                    type: 'POST',
                    data: {
                        submittedAnswer: userAnswer,
                        submittedId: equationId
                    }
                }).done(response => {
                }).fail((xhr, status, error) => {
                    console.error(error);
                });
            });

            renderEquations();
        });


    </script>
    <script src="../scripts/global.js"></script>
    <script type="module" src="../languages/languageSwitching.js"></script>
</body>

</html>