<?php
require("../api/equationFunctionionality.php");

$result = $conn->query("SELECT id,submittedAnswer FROM StudentAssignmentLink");

$sidebarItems = array();

if ($result->num_rows > 0) {
    $i = 0;
    while ($row = $result->fetch_assoc()) {
        $assignmentId = $row['id'];
        $url = "https://site215.webte.fei.stuba.sk/semestralka/views/equations.php?i=$i";
        $sidebarItems[$i] = $url;

        $i++;

    }
}

// Use the $sidebarItems array as needed
foreach ($sidebarItems as $assignmentId => $url) {
}
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

        .sidebar {
            width: 200px;
            background-color: #f0f0f0;
            padding: 10px;
        }

        .sidebar ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }

        .sidebar li {
            margin-bottom: 5px;
        }

        .sidebar a {
            display: block;
            padding: 5px;
            background-color: #ddd;
            text-decoration: none;
            color: #333;
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
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="#">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../api/logout.php">Logout</a>
                        </li>
                        <li class="nav-item float-right">
                            <a class="nav-link" href="../api/logout.php">AAAA</a>
                        </li>
                    </ul>

                </div>
            </div>
        </nav>
    </header>
    <div class="container-fluid">
        <?php if (isset($_GET['i'])) { ?>
            <?php
            echo $blokovkaLogic;

            ?>
            <div id="mathquill-editor"></div>
            <button id="submit-btn" data-translate="submit">Submit</button>
        </div>
        <div class="sidebar">
            <ul>
                <?php foreach ($sidebarItems as $label => $url) { ?>
                    <li><a href="<?php echo $url; ?>"><?php echo $label; ?></a></li>
                <?php } ?>
            </ul>
        </div>
    <?php } else {

            echo ($generateButton);

        } ?>
    <script type="text/javascript">
        $(document).ready(function () {
            var MQ = MathQuill.getInterface(2);

            var mathField = MQ.MathField(document.getElementById('mathquill-editor'), {
                handlers: {
                    edit: function () {
                        // Perform any custom logic when the content of the MathQuill editor changes
                    }
                },
                restrictMismatchedBrackets: true
            });

            // Add event listener to the submit button
            $('#submit-btn').on('click', function () {
                var userAnswer = mathField.latex();

                $.ajax({
                    url: '../api/submitAnswer.php',
                    type: 'POST',
                    data: { submittedAnswer: userAnswer },
                    success: function (response) {
                        console.log('Assignment ID:', response.assignmentId);
                        console.log('Submitted Answer:', response.submittedAnswer);
                        console.log('Student id:', response.studentId);
                    },
                    error: function (xhr, status, error) {
                        console.error(error);
                    }
                });
            });



        });
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
        function toggleGeneration() {
            // Make an AJAX request to the PHP file with the session ID
            var xhr = new XMLHttpRequest();
            xhr.open("GET", "../api/get_random_ids.php", true);

            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    // Handle the response from the PHP file
                    var response = xhr.responseText;
                    // Perform any necessary actions with the response
                    console.log(response);
                }
            };
            xhr.send();
            location.href = "https://site215.webte.fei.stuba.sk/semestralka/views/equations.php?i=0";
        }




        window.addEventListener('load', renderEquations);
    </script>
    <script type="module" src="../languages/languageSwitching.js"></script>
</body>

</html>