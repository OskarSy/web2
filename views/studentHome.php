<?php
require_once("../api/config.php");

if (empty($_SESSION["id"])) {
    header("Location: https://site215.webte.fei.stuba.sk/semestralka/");
}
$result = $conn->query("SELECT a.id
                        FROM Assignments a
                        INNER JOIN AssignmentGroup ag ON a.groupId = ag.id
                        WHERE ag.canBeUsed = '1'");

$ids = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $ids[] = $row['id'];
    }
}
$_SESSION['availableEquations'] = $ids;
$_SESSION['generationMax'] = $generationMax = count($ids);
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
    <title>Home</title>
</head>

<body>
    <div class="container-fluid wrapper">
        <div class="row">
            <div class="col-10 mx-auto">
                <button id="togglegeneration" onclick="toggleGeneration()" data-translate="generateEQ">Generate equations</button>
                <input type="number" id="inputValue" min="1" max="<?php echo $generationMax ?>">
                <div id="card-container"></div>
            </div>
        </div>
    </div>
    <div class="modal text-dark" tabindex="-1" id="myModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" data-translate="loginError">loginError</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <span id="modalError" data-translate="emptyGenerationCount"></span>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        function generateCards(elements) {
            console.log(elements);
            const container = document.getElementById('card-container');
            container.innerHTML = ''; // Clear the container before generating new cards

            let row = document.createElement('div');
            row.classList.add('row');

            elements.forEach((element, index) => {
                const card = document.createElement('div');
                card.classList.add('col');
                card.classList.add('mb-4'); // Add some margin at the bottom of each card

                card.innerHTML = `
      <div class="card" data-id="${element.id}">
        <div class="card-body">
        ${element.equation}
        </div>
      </div>
    `;

                row.appendChild(card);
                if ((index + 1) % 3 === 0 && window.innerWidth > 768) {
                    container.appendChild(row);
                    row = document.createElement('div');
                    row.classList.add('row');
                }
            });

            container.appendChild(row);
        }        

        function toggleGeneration() {
            var generationCount = document.getElementById("inputValue").value;
            if (generationCount > 0) {
                $.ajax({
                    url: '../api/get_random_ids.php',
                    type: 'POST',
                    contentType: "application/json",
                    data: JSON.stringify({
                        generationCount: generationCount
                    })
                }).done(response => {         
                    console.log(JSON.parse(response));           
                    // generateCards(JSON.parse(response));
                }).fail((xhr, status, error) => {
                    console.error(error);
                });
            } else {
                modal.show();
            }
        }
    </script>
</body>

</html>