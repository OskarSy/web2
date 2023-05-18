<?php

require_once("../api/config.php");

if(!isset($_SESSION["role"]) || $_SESSION["role"] !== "teacher"){
    header("Location: ../index.php");
    exit;
}

$quey = "SELECT * FROM Student s;";
$stmt = $conn->prepare($quey);
$stmt->execute();

$result = $stmt->get_result(); // Get the mysqli result

$students = array();
$tasks = array();
while ($row = $result->fetch_assoc()) {
    $students[] = $row;
}

$showTable = false;
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['selectStudent'])) {
    $selectedStudentId = $_POST['selectStudent'];
    $showTable = True;
    var_dump($selectedStudentId);

    $myQ = 'SELECT ag.name, ass.correctAnswer, sal.submittedAnswer, 
                    IFNULL(sal.achievedPoints, 0) AS achievedPoints, 
                    IF(sal.achievedPoints IS NULL, "Nespravna", "Spravna") AS correct 
            FROM Assignments as ass 
            INNER JOIN AssignmentGroup as ag ON ass.groupId = ag.id 
            INNER JOIN StudentAssignmentLink as sal ON ass.id = sal.assignmentId 
            WHERE sal.studentId = ' . $selectedStudentId .';
    ';
    $stmt_s = $conn->prepare($myQ);
    $stmt_s->execute();
    $result_s = $stmt_s->get_result(); // Get the mysqli result

    while ($row = $result_s->fetch_assoc()) {
        $tasks[] = $row;
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <link href="../styles/all.css" rel="stylesheet">

    <style>
        .container {
            max-width: 800px !important;
        }
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
    <?php require_once("../templates/navbar.php") ?>
    <div class="container justify-content-center align-items-start" style="margin-top: 50px; margin-bottom: 50px; max-width: 1200px;">
        <div class="container-md">            
            
            <h5 class="text-uppercase m-5" data-translate="generatedTasksStats">Prehladna tabulka o studentov</h5>
            
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="flex-grow-1 mr-3 w-70">
                        <select name="selectStudent" class="form-select form-select-lg w-100" aria-label=".form-select-lg example">
                            <option selected data-translate="pickStudent">Vyberte studenta</option>
                            <?php
                            foreach($students as $result) {
                                echo '<option value="'. $result["id"] .'">'  . $result["name"] . $result["surname"] . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <button type="submit" name="showTaskko" class="btn btn-primary align-self-stretch" data-translate="showTheTasks">Ukazat</button>
                </div>
            </form>

            <table class="table align-middle mb-0 bg-white">
                    <thead class="bg-light">
                        <tr>
                        <th data-translate="setOfTasks">Sada prikladov</th>
                        <th data-translate="correctAnswer">Spravny odpoved</th>
                        <th data-translate="submittedS">Odovzdal</th>
                        <th data-translate="pointsS">Body</th>
                        <th data-translate="correctSubmission">Spravnost</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach($tasks as $result) {
                            echo '
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                        <div class="ms-3">
                                            <p class="text-muted mb-0"> '. $result["name"] . '</p>
                                        </div>
                                        </div>
                                    </td>
                                    <td>
                                        <p class="text-muted mb-0">' . $result["correctAnswer"] . '</p>
                                    </td>
                                    <td>
                                        <p class="text-muted mb-0">' . $result["submittedAnswer"] . '</p>
                                    </td>
                                    <td>
                                        <p class="text-muted mb-0">' . $result["achievedPoints"] . '</p>
                                    </td>
                                    <td>
                                        <p class="text-muted mb-0">' . $result["correct"] . '</p>
                                    </td>
                                </tr>';
                        }
                        ?>
                    </tbody>
                </table>

        </div><br>

        <div class="d-flex justify-content-center mt-2">
            <a href="teacher.php" class="btn btn-primary btn-lg w-100" role="button" data-translate="backButton">Naspať</a>
        </div>

    </div>
    

    <script type="module" src="../languages/languageSwitching.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.2.0/mdb.min.js" type="text/javascript" ></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe"
        crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#myTable1').DataTable();
        });
    </script>
</body>

</html>
