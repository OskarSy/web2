<?php

require_once("../api/config.php");

if(!isset($_SESSION["role"]) || $_SESSION["role"] !== "teacher"){
    header("Location: ../index.php");
    exit;
}

$quey = "SELECT s.id, s.name, s.surname, COUNT(sal.assignmentId) AS assignmentCount,
                                        COUNT(sal.submittedAnswer) AS submittedAnswerCount,
                                        SUM(sal.achievedPoints) AS totalAchievedPoints
        FROM Student s
        LEFT JOIN StudentAssignmentLink sal ON s.id = sal.studentId
        GROUP BY s.id, s.name, s.surname;";
$stmt = $conn->prepare($quey);
$stmt->execute();

$result = $stmt->get_result(); // Get the mysqli result

$students = array();
while ($row = $result->fetch_assoc()) {
    $students[] = $row;
}

// Export to CSV
if (isset($_POST['export'])) {
    header('Content-Encoding: UTF-8');
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="students.csv"');
    echo "\xEF\xBB\xBF"; // Add BOM to ensure correct encoding in Excel
    $output = fopen('php://output', 'w');
    fputcsv($output, array('Meno', 'Priezvisko', 'ID', 'Vygeneroval', 'Odovzdal', 'Body'));

    foreach ($students as $student) {
        fputcsv($output, $student);
    }
    fclose($output);
    exit();
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
            
            <h5 class="text-uppercase m-5" data-translate="studentStatsTable">Prehladna tabulka o studentov</h5>
            <table id="myTable1" class="table table-striped">
                <thead>
                    <tr>
                        <th data-translate="nameS">Meno</th>
                        <th data-translate="surnameS">Priezvisko</th>
                        <th data-translate="idS">ID</th>
                        <th data-translate="generatedS">Vygeneroval</th>
                        <th data-translate="submittedS">Odovzdal</th>
                        <th data-translate="pointsS">Body</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach($students as $student){
                        echo '<tr>
                                <td>' . $student["name"] . '</td>
                                <td>' . $student["surname"] . '</td>
                                <td>' . $student["id"] . '</td>
                                <td>' . $student["assignmentCount"] . '</td>
                                <td>' . $student["submittedAnswerCount"] . '</td>
                                <td>' . $student["totalAchievedPoints"] . '</td>
                            </tr>';
                    }
                    ?>
                </tbody>
            </table>

            <form method="post">
                <button type="submit" name="export" class="btn btn-primary" data-translate="exportko">Export to CSV</button>
            </form>
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
            $('#myTable1').DataTable({
                "searching": false,
                "paging": false,
                "lengthChange": false,
                "info": false,
                "pageLength": 1000000
            });
        });
    </script>
</body>

</html>
