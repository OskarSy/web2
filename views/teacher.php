<?php

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
    <section>
        <div>
            <!-- TODO: teacher formulars -->
            <div class="container justify-content-center align-items-start" style="margin-top: 50px; margin-bottom: 50px; max-width: 1200px;">
                <div class="row d-flex justify-content-center">
                    <div class="col-md-6">

                        <h3 class="text-uppercase text-center m-5">Context</h3>
                            
                    </div>  
                </div>
                    <hr class="my-5">
                <div class="row d-flex justify-content-center">
                    <div class="col-md-6">
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script type="module" src="../languages/languageSwitching.js"></script>
</body>

</html>