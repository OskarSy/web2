<?php
require_once('../api/config.php');
require_once('../api/equationFunctionionality.php');



session_start();
if (empty($_SESSION["id"])) {
    header("Location: ../index.php");
}
if ($_SESSION['role'] == 'student' && isset($_SESSION['id'])) {
    $studentId = $_SESSION['studentId'];
} else {
    header("Location: ../index.php");
}


$lastGenerationIndex = $conn->query("SELECT MAX(generationIndex) AS max
    FROM StudentAssignmentLink WHERE studentId = '$studentId'");
if ($lastGenerationIndex->num_rows > 0) {
    $generationIndex = $lastGenerationIndex->fetch_assoc()['max'];
} 
if($generationIndex==null){
    $generationIndex = 0;
}

$generatedEquations = getAllGeneratedEquations($studentId, $generationIndex);
$ids = array();
$stmt = $conn->prepare("SELECT a.id, ag.name
            FROM Assignments a
            INNER JOIN AssignmentGroup ag ON a.groupId = ag.id            
            WHERE ag.canBeUsed = '1'
            AND a.id NOT IN (SELECT assignmentId
                FROM StudentAssignmentLink
                WHERE studentId = ?
                AND generationIndex = ?
                AND submittedAnswer IS NOT NULL) ");
$stmt->bind_param('ii', $studentId, $generationIndex);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $availableIds[] = array('name'=>$row['name'],'id'=>$row['id']);
    }
} else {
    $generationIndex++;
}
$_SESSION['generationIndex'] = $generationIndex;
$_SESSION['availableEquations'] = $availableIds;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/mathquill/0.10.1/mathquill.min.css">
    <script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.0/MathJax.js?config=TeX-MML-AM_HTMLorMML"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.6.4.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
    <link href="../styles/all.css" rel="stylesheet">
    <link rel="stylesheet" href="../styles/studentHome.css">
    <title>Home</title>
</head>

<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">
                    <?php echo $_SESSION['email'] ?>
                </a>
                <button class="btn btn-sm btn-secondary languageSwitcher me-1" data-language="sk">Slovenčina</button>
                <button class="btn btn-sm btn-secondary languageSwitcher" data-language="en">English</button>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../api/logout.php">Logout</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    <div class="container-fluid" id="wrapper">
        <div class="row">
            <div class="col-10 mx-auto mt-4">
                <div class="mb-4 mx-auto d-flex flex-column col-12 col-md-3 col-lg-2">
                    <select class="form-control my-2" id="selectGroup">
                        <?php
                            $availableGroups = $conn->query("SELECT id,name
                            FROM AssignmentGroup WHERE canBeUsed = 1");
                            if ($availableGroups->num_rows > 0) {
                                $noGroups = false;
                                while ($row = $availableGroups->fetch_assoc()) {
                                    echo '<option value="' . $row['name'] . '" >' . $row['name'] . '</option>';
                                }
                            }
                            else{
                                $noGroups = true;
                            }
                        ?>
                    </select>
                    <button type="button" class="btn btn-primary" id="toggleGeneration" data-translate="generateEQ" <?php $noGroups ? 'disabled' : '' ?>>Generate equation</button>
                </div>
                <div id="card-container">

                </div>
            </div>
        </div>
    </div>

    <script>
        $('#toggleGeneration').click(() => {
            toggleGeneration();
        });
        <?php
        if ($generatedEquations!=null) {
            echo "generateCards(" . json_encode($generatedEquations) . ");";
        }
        ?>

        function generateCards(elements) {
            console.log(elements);
            const container = document.getElementById('card-container');
            container.innerHTML = '';
            let row = document.createElement('div');
            row.classList.add('row');
            elements.forEach((element, index) => {
                const card = document.createElement('div');
                card.classList.add('col');
                card.classList.add('mb-4');
                console.log(element.isSubmitted);
                card.innerHTML = `
                <div class="card h-100" data-id="${element.id}">                    
                    <div class="card-body content">
                        <div class="overlay card" ${element.isSubmitted ? '' : 'hidden'}>
                            <h1 class="my-auto mx-auto text-light">Solved</h1>
                        </div>
                        <div class="highlight" hidden>
                        </div>                    
                        ${element.equation}
                        ${element.img ?? ''}
                    </div>
                </div>`;
                const highlight = card.getElementsByClassName('highlight').item(0);
                const cardBody = card.getElementsByClassName('card-body').item(0);
                cardBody.addEventListener('mouseenter', e => {
                    $(highlight).prop('hidden', false);
                });

                cardBody.addEventListener('mouseleave', function() {
                    $(highlight).prop('hidden', true);
                });

                row.appendChild(card);
                if ((index + 1) % 3 === 0 && window.innerWidth > 768) {
                    container.appendChild(row);
                    row = document.createElement('div');
                    row.classList.add('row');
                }
            });

            container.appendChild(row);
            <?php
            if (!isset($_SESSION['generationIndex'])) {
                echo ("renderEquations();");
            } ?>
            $('.card').click(card => {
                window.location.href = "./equations.php?equationId=" + $(card.target).closest('.card').data('id');
            });
        }

        function toggleGeneration() {

            let name = $('#selectGroup').val();

            $.ajax({
                url: '../api/get_random_ids.php',
                type: 'POST',
                contentType: "application/json",
                data: JSON.stringify({
                    name: name
                })
            }).done(response => {
                console.log(response);
                console.log(JSON.parse(response));
                generateCards(JSON.parse(response));
                $('#inputValue').val(null);
            }).fail((xhr, status, error) => {
                console.error(error);
            });
        }
    </script>
    <script src="../scripts/global.js"></script>
    <script type="module" src="../languages/languageSwitching.js"></script>
</body>

</html>