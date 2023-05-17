<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once("../api/config.php");

$groupNames = "SELECT a.name FROM AssignmentGroup a;";
$stmt_grnames = $conn->prepare($groupNames);
$stmt_grnames->execute();
$grresult = $stmt_grnames->get_result();

$grnames = array();
while ($row = $grresult->fetch_assoc()) {
    $grnames[] = $row;
}

function updateAssignmentGroup($conn, $dateToPost1, $dateToPost2, $canBeUsed, $id) {
    $ass_group_sql = "UPDATE AssignmentGroup SET canBeUsedFrom = ?, canBeUsedTo = ?, canBeUsed = ? WHERE id = ?";
    $stmt_assGroup = $conn->prepare($ass_group_sql);
    $stmt_assGroup->bind_param("ssii", $dateToPost1, $dateToPost2, $canBeUsed, $id);
    $stmt_assGroup->execute();
}

function updateAssignmentGroupPoints($conn, $name, $id) {
    $ass_group_sql = "UPDATE AssignmentGroup SET maxPoints = ? WHERE id = ?";
    $stmt_assGroup = $conn->prepare($ass_group_sql);
    $stmt_assGroup->bind_param("si", $name, $id);
    $stmt_assGroup->execute();
}

// TODO: second date is smaller than first + only one of the dates were given
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['generateTasks'])) {
    $checkbox1 = isset($_POST['checkbox1']) ? 1 : 0;
    $checkbox2 = isset($_POST['checkbox2']) ? 1 : 0;
    $checkbox3 = isset($_POST['checkbox3']) ? 1 : 0;
    $checkbox4 = isset($_POST['checkbox4']) ? 1 : 0;

    $datePicker1 = isset($_POST['myDatePicker1']) && !empty($_POST['myDatePicker1']) ? $_POST['myDatePicker1'] : null;
    $datePicker2 = isset($_POST['myDatePicker2']) && !empty($_POST['myDatePicker2']) ? $_POST['myDatePicker2'] : null;

    if($datePicker1 && $datePicker2) {
        $myDate1 = new DateTime($datePicker1);
        $myDate2 = new DateTime($datePicker2);
        if($datePicker2 < $datePicker1) {
            $correctDate = 0;
            echo "The second date is earlier than the first date.";
        } else {
            $correctDate = 1;
        }
    } else {
        $correctDate = 0;
        echo "One or both of the dates are not set.";
    }

    if ($checkbox1 && $correctDate) {
        $dateToPost1 = $datePicker1;
        $dateToPost2 = $datePicker2;
    } else {
        $dateToPost1 = null;
        $dateToPost2 = null;
    }
    updateAssignmentGroup($conn, $dateToPost1, $dateToPost2, $checkbox1, 8);

    if ($checkbox2 && $correctDate) {
        $dateToPost1 = $datePicker1;
        $dateToPost2 = $datePicker2;
    } else {
        $dateToPost1 = null;
        $dateToPost2 = null;
    }
    updateAssignmentGroup($conn, $dateToPost1, $dateToPost2, $checkbox2, 9);

    if ($checkbox3 && $correctDate) {
        $dateToPost1 = $datePicker1;
        $dateToPost2 = $datePicker2;
    } else {
        $dateToPost1 = null;
        $dateToPost2 = null;
    }
    updateAssignmentGroup($conn, $dateToPost1, $dateToPost2, $checkbox3, 10);

    if ($checkbox4 && $correctDate) {
        $dateToPost1 = $datePicker1;
        $dateToPost2 = $datePicker2;
    } else {
        $dateToPost1 = null;
        $dateToPost2 = null;
    }
    updateAssignmentGroup($conn, $dateToPost1, $dateToPost2, $checkbox4, 11);
    
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['definePoints'])) {
    $blokovka1 = isset($_POST['blokovka1']) && !empty($_POST['blokovka1']) && is_numeric($_POST['blokovka1']) ? $_POST['blokovka1'] : null;
    $blokovka2 = isset($_POST['blokovka2']) && !empty($_POST['blokovka2']) && is_numeric($_POST['blokovka2']) ? $_POST['blokovka2'] : null;
    $odozva1 = isset($_POST['odozva1']) && !empty($_POST['odozva1']) && is_numeric($_POST['odozva1']) ? $_POST['odozva1'] : null;
    $odozva2 = isset($_POST['odozva2']) && !empty($_POST['odozva2']) && is_numeric($_POST['odozva2']) ? $_POST['odozva2'] : null;

    if ($blokovka1) {
        updateAssignmentGroupPoints($conn, $blokovka1, 8);
    }
    if ($blokovka2) {
        updateAssignmentGroupPoints($conn, $blokovka2, 9);
    }
    if ($odozva1) {
        updateAssignmentGroupPoints($conn, $odozva1, 10);
    }
    if ($odozva2) {
        updateAssignmentGroupPoints($conn, $odozva2, 11);
    }
    
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
        <?php require_once("../templates/navbar.php") ?>
    <section>
        <div>
            <div class="container justify-content-center align-items-start" style="margin-top: 50px; margin-bottom: 50px; max-width: 1200px;">
                <div class="row d-flex justify-content-center">
                    <div class="col-md-12">
                        
                        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                            <h3 class="text-uppercase text-center m-5" data-translate="genExamples">Nastavenie generovanie prikladov pre studenta</h3>

                            <div class="form-outline mb-4">
                                <div class="form-outline tea">
                                    <input
                                        type="text"
                                        name="myDatePicker1"
                                        class="form-control"
                                        id="datePicker1"
                                        value="<?php echo $person['birth_day'] ?>"
                                    />
                                    <label for="datePicker1" class="form-label" data-translate="showFrom" >Generovanie od</label>
                                </div>
                            </div>

                            <div class="form-outline mb-4">
                                <div class="form-outline tea">
                                        <input
                                            type="text"
                                            name="myDatePicker2"
                                            class="form-control"
                                            id="datePicker2"
                                            value=""
                                        />
                                        <label for="datePicker2" class="form-label" data-translate="showTo">Generovanie do</label>
                                </div>
                            </div>
                            <div style="display: flex; justify-content: center;">
                                <div class="form-check">
                                    <input name="checkbox1" class="form-check-input" type="checkbox" value="1" id="checkbox1"/>
                                    <label class="form-check-label" for="checkbox1" >Blokovka1</label>
                                </div>

                                <div class="form-check">
                                    <input name="checkbox2" class="form-check-input" type="checkbox" value="1" id="checkbox2"/>
                                    <label class="form-check-label" for="checkbox2">Blokovka2</label>
                                </div>

                                <div class="form-check">
                                    <input name="checkbox3" class="form-check-input" type="checkbox" value="1" id="checkbox3"/>
                                    <label class="form-check-label" for="checkbox3">Odozva1</label>
                                </div>

                                <div class="form-check">
                                    <input name="checkbox4" class="form-check-input" type="checkbox" value="1" id="checkbox4"/>
                                    <label class="form-check-label" for="checkbox4">Odozva2</label>
                                </div>
                            </div>

                            <div class="d-flex justify-content-center mt-2">
                                <button name="generateTasks" class="btn btn-primary btn-lg btn-block" type="submit" data-translate="setUp">Nastavit</button>
                            </div>
                        </form>
                    </div>  
                
                </div>
                <hr class="my-4">
                <div class="row d-flex justify-content-center">
                    <div class="col-md-12">
                        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                            <h3 class="text-uppercase text-center m-5" data-translate="tasksPoints">Hodnoty príklady súborov</h3>
                            
                            <select name="selectgroup" class="form-select form-select-lg mb-3" aria-label=".form-select-lg example">
                                <option selected>Vyberte sadu</option>
                                <?php
                                foreach($grnames as $result) {
                                    echo '<option value="' . $result["name"] . '">' . $result["name"] . '</option>';
                                }
                                ?>
                            </select>

                            <div class="form-outline mb-4">
                                <div class="form-outline tea">
                                    <input name="mygroup" type="number" class="form-control" value="" required/>
                                    <label class="form-label" for="mygroup" value=""></label>
                                </div>
                            </div>

                            <div class="d-flex justify-content-center mt-2">
                                <button name="definePoints" class="btn btn-primary btn-lg btn-block" type="submit" data-translate="setUpPoints">Nastavit body</button>
                            </div>
                        </form>
                    </div>
                </div>
                <hr class="my-4">
                <div class="row d-flex justify-content-center">
                    <div class="col-md-12">
                        <h3 class="text-uppercase text-center m-5" data-translate="showTables">Prezeranie tabuliek</h3>

                        <div class="d-flex justify-content-around mt-2">
                            <a href="showStudents.php" class="btn btn-primary btn-lg" role="button" data-translate="showStudents">Show students</a>
                            <a href="" class="btn btn-primary btn-lg" role="button" data-translate="showTasks">Show tasks</a>
                        </div>

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
        window.onload = function() {
            const selectElement = document.querySelector('select[name="selectgroup"]');
            const buttonElement = document.querySelector('button[name="definePoints"]');
            const inputElement = document.querySelector('input[name="mygroup"]');
            const labelElement = document.querySelector('label[for="mygroup"]');

            inputElement.style.display = "none";
            buttonElement.style.display = "none";

            selectElement.addEventListener('change', function(event) {
                if (event.target.value !== "Vyberte sadu") {
                    inputElement.style.display = "block";
                    buttonElement.style.display = "block";
                    inputElement.id = event.target.value;
                    labelElement.textContent = event.target.options[event.target.selectedIndex].text;
                } else {
                    inputElement.style.display = "none";
                    buttonElement.style.display = "none";
                    labelElement.textContent = "";
                }
            });
        }
    </script>
</body>

</html>