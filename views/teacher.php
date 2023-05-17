<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once("../api/config.php");
//require_once __DIR__ . "/../api/config.php";

//$conn = mysqli_connect($hostname, $username, $password, $dbname);
$query_students = "SELECT * FROM Student";
$studentResults = mysqli_query($conn, $query_students);
$results_s = array();

while ($row = mysqli_fetch_assoc($studentResults)) {
    $results_s[] = $row;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Equations</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.2.0/mdb.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/vanillajs-datepicker@1.3.1/dist/css/datepicker-bs5.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/mathquill/0.10.1/mathquill.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
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

        .form-outline.tea {
            background-color: white;
        }

        .form-check {
            border-right: 1px solid black;
            padding-right: 10px;
            margin-right: 10px;
        }

        .form-check:last-child {
            border-right: none;
            padding-right: 0;
            margin-right: 0;
        }
    </style>
</head>

<body id="wrapper">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mathquill/0.10.1/mathquill.min.js"></script>
        <?php require_once("../templates/navbar.php") ?>
    <section>
        <div>
            <div class="container justify-content-center align-items-start" style="margin-top: 50px; margin-bottom: 50px; max-width: 1200px;">
                <div class="row d-flex justify-content-center">
                    <div class="col-md-12">
                        
                        <h3 class="text-uppercase text-center m-5">Nastavenie generovanie prikladov pre studenta</h3>
                        <!-- <select name="selectperson" class="form-select form-select-lg mb-3" aria-label=".form-select-lg example">
                            <option selected>Vyberte športovca</option>
                            <?php
                            // foreach($results_s as $result) {
                                // echo '<option value="' . $result["id"] . '">' . $result["name"] . ' ' . $result["surname"] . '</option>';
                            // }
                            ?>
                        </select> -->
                        <div class="form-outline mb-4">
                            <div class="form-outline tea">
                                <input
                                    type="text"
                                    name="birthDay"
                                    class="form-control"
                                    id="datePicker1"
                                    value="<?php echo $person['birth_day'] ?>"
                                />
                                <label for="datePicker1" class="form-label">Generovanie od</label>
                            </div>
                        </div>

                        <div class="form-outline mb-4">
                            <div class="form-outline tea">
                                    <input
                                        type="text"
                                        name="deathDay"
                                        class="form-control"
                                        id="datePicker2"
                                        value=""
                                    />
                                    <label for="datePicker2" class="form-label">Generovanie do</label>
                            </div>
                        </div>
                        <div style="display: flex; justify-content: center;">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="checkbox1"/>
                                <label class="form-check-label" for="checkbox1">Blokovka1</label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="checkbox2"/>
                                <label class="form-check-label" for="checkbox2">Blokovka2</label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="checkbox3"/>
                                <label class="form-check-label" for="checkbox3">Odozva1</label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="checkbox4"/>
                                <label class="form-check-label" for="checkbox4">Odozva2</label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-center mt-2">
                            <button name="addperson" class="btn btn-primary btn-lg btn-block" type="submit">Nastavit</button>
                        </div>
                    </div>  
                    
                </div>
                <hr class="my-4">
                <div class="row d-flex justify-content-center">
                    <div class="col-md-12">
                        <h3 class="text-uppercase text-center m-5">Hodnoty príklady súborov</h3>

                        <div class="form-outline mb-4">
                            <div class="form-outline tea">
                                <input type="number" name="placinginput" id="blokovka1" class="form-control" value="" required/>
                                <label class="form-label" for="blokovka1" value="">Blokovka1</label>
                            </div>
                        </div>

                        <div class="form-outline mb-4">
                            <div class="form-outline tea">
                                <input type="number" name="placinginput" id="blokovka2" class="form-control" value="" required/>
                                <label class="form-label" for="blokovka2" value="">Blokovka2</label>
                            </div>
                        </div>

                        <div class="form-outline mb-4">
                            <div class="form-outline tea">
                                <input type="number" name="placinginput" id="odozva1" class="form-control" value="" required/>
                                <label class="form-label" for="odozva1" value="">Odozva1</label>
                            </div>
                        </div>

                        <div class="form-outline mb-4">
                            <div class="form-outline tea">
                                <input type="number" name="placinginput" id="odozva2" class="form-control" value="" required/>
                                <label class="form-label" for="odozva2" value="">Odozva2</label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-center mt-2">
                            <button name="addperson" class="btn btn-primary btn-lg btn-block" type="submit">Nastavit body</button>
                        </div>
                    </div>
                </div>
                <hr class="my-4">
                <div class="row d-flex justify-content-center">
                    <div class="col-md-12">
                        <h3 class="text-uppercase text-center m-5">Hodnoty príklady súborov</h3>
                    <div>
                </div>
            </div>
        </div>
    </section>
    <script type="module" src="../languages/languageSwitching.js"></script>
    <script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.0/MathJax.js?config=TeX-MML-AM_HTMLorMML"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.2.0/mdb.min.js" type="text/javascript" ></script>
    <script src="https://cdn.jsdelivr.net/npm/vanillajs-datepicker@1.3.1/dist/js/datepicker.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe"
        crossorigin="anonymous"></script>
    <script>
        const datePickerElement1 = document.getElementById('datePicker1');
        const datepicker = new Datepicker(datePickerElement1, {
            buttonClass: 'btn',
            format: 'yyyy-mm-dd'
        }); 

        const datePickerElement2 = document.getElementById('datePicker2');
        const datepicker2 = new Datepicker(datePickerElement2, {
            buttonClass: 'btn',
            format: 'yyyy-mm-dd'
        }); 
    </script>
</body>

</html>