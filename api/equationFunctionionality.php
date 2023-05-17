<?php
require_once ('config.php');


$task = "";
$taskSymbols = "";
$solutions = "";
$systemDescription = "";
$imagePath = "";
$descSymbols = "";

$generatedCount = [];


if (isset($_GET["i"])) {
    $userIndex=$_SESSION['userIndex'];
    $assignmentId = $_GET["i"];
    $user = $_SESSION["studentId"];
    $result = $conn->query("SELECT assignmentId FROM StudentAssignmentLink WHERE studentId = '$user' and userIndex='$userIndex'");
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $generatedCount[] = $row['assignmentId'];
        }
    }
    if (!empty($generatedCount[$assignmentId])) {
        $randomNumber = $generatedCount[$assignmentId];
    } else {
        echo ("<script>alert('No generated count found for the given assignment ID.'); window.location.href = 'https://site215.webte.fei.stuba.sk/semestralka/views/equations.php';</script>");


    }
    $_SESSION['currentEquation']=$randomNumber;
    $result = $conn->query("SELECT * FROM Assignments WHERE id='$randomNumber'");
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $task = $row['body'];
        $solutions = $row['correctAnswer'];
        $groupId = $row['groupId'];
    } else {
        echo ("error Assignments query");
    }
    $tasks = $task;


    $result = $conn->query("SELECT name FROM AssignmentGroup WHERE id='$groupId'");
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $fileName = $row['name'];
    } else {
        echo ("error AssignmentGroup query");
    }



    function isBlokovka($fileName)
    {
        $fileName = preg_replace('/\d+/', '', $fileName);
        if ($fileName == "blokovka") {
            return true;
        } else if ($fileName == "odozva") {
            return false;
        }
    }

    function createEquation($equation1)
    {
        $pattern = '/funkciu(.+)/i';
        preg_match($pattern, $equation1, $matches);
        $equation = $matches[1];
        $pattern = '/(.*?)\spre\b/';
        preg_match($pattern, $equation, $matches);
        $equation = $matches[1];
        return $equation;
    }




    function createTask($equation1, $toRemove)
    {
        $pattern = '/(.*?)\spre\b/';
        preg_match($pattern, $equation1, $matches);
        $equation = $matches[1];
        $task = explode($toRemove, strval($equation));
        return $task[0];
    }





    function createDescription($equation, $equation1)
    {
        $systemDescription = explode(strval($equation), $equation1);
        $pattern = '/zadanie99\//';
        $replacement = '../equations/';
        $systemDescription = preg_replace($pattern, $replacement, $systemDescription[1]);
        $pattern = '/includegraphics/';
        $replacement = '';
        $modifiedText = preg_replace($pattern, $replacement, $systemDescription);
        $pattern = '/\\\\/';
        $replacement = '';
        $modifiedText = preg_replace($pattern, $replacement, $modifiedText);
        $pattern = '/{/';
        $replacement = '';
        $modifiedText = preg_replace($pattern, $replacement, $modifiedText);
        $pattern = '/}/';
        $replacement = '';
        $modifiedText = preg_replace($pattern, $replacement, $modifiedText);
        return $modifiedText;
    }







    function forBlokovka2($task, &$tasks, &$taskSymbols, &$descSymbols, &$systemDescriptions)
    {
        $systemDescriptions = createTask2($tasks);

        $descSymbols = explode($systemDescriptions, $tasks);
        $descSymbols = $descSymbols[1];

        $result = explode($descSymbols, $tasks);
        $pattern = '/(.*?)(\sna.*)/i';
        $result = preg_replace($pattern, '', $result[0]);
        $delimiter = "rovnicou";
        $result = explode($delimiter, $result);
        $tasks = $result[0];
        $taskSymbols = $result[1];

    }
    function forBlokovka1($equation, &$tasks, &$taskSymbols, $task)
    {
        $buffer = $equation . " funkciou";
        $taskSymbols = explode($buffer, $tasks);
        $taskSymbols = $taskSymbols[1];
        $tasks = $buffer;
    }

    function createTask2($equation1)
    {
        $pattern = '/na(.*)/i';
        preg_match($pattern, $equation1, $matches);
        $result = trim($matches[0]);
        $result = str_replace('\\\\', '', $result);
        return $result;
    }



    function od1($fileName)
    {

        if ($fileName == "odozva01") {
            return true;
        } else if ($fileName == "odozva02") {
            return false;
        }
    }



    if (!isBlokovka($fileName)) {
        $pattern = '/(.*?)\srovnicou\b/';
        preg_match($pattern, $task, $matches);
        $equation = $matches[1];
        $buffer = $equation . " rovnicou";
        $task = $buffer;

        if (od1($fileName)) {
            forBlokovka1($equation, $tasks, $taskSymbols, $task);
        } else {
            forBlokovka2($task, $tasks, $taskSymbols, $descSymbols, $systemDescriptions);
        }


    } else {
        $pattern = '/\\\\dfrac/';
        $replacement = '\\\\frac';
        $equation1 = preg_replace($pattern, $replacement, $task);
        $taskSymbols = createEquation($equation1);
        $task = createTask($equation1, $taskSymbols);
        $buffer = createDescription($task, $equation1);
        $buffer = explode(": ", $buffer);
        $imagePath = $buffer[1];
        $systemDescriptions = $buffer[0];

    }



    if (isBlokovka($fileName)) {
        $blokovkaLogic = '
<div id="task" class="text-center">';
        $blokovkaLogic .= '
    <p data-translate="EqTask">' . $task . '</p>
    <p>' . "\(" . $taskSymbols . "\)" . '</p>
    <p data-translate="EqDescription">' . $systemDescriptions . '</p>
    <div class="image-container">
        <img src="' . $imagePath . '" alt="' . $systemDescriptions . '" class="img-fluid">
    </div>';
        $blokovkaLogic .= '
</div>
<div id="solution" style="display: none;">';
        $blokovkaLogic .= '
    <p>' . "\(" . $solutions . "\)" . '</p>';
        $blokovkaLogic .= '
</div>
<button id="toggleSolution" onclick="toggleSolution()" data-translate="solution">Show solution</button>';
    } else {




        $blokovkaLogic = '
<div id="task" class="text-center">';
        if (!od1($fileName)) {
            $blokovkaLogic .= '
    <p data-translate="EqOdozva2Task">' . $task . '</p>
    <p>' . "\(" . $taskSymbols . "\)" . '</p>
    <p data-translate="EqOdozva2Description">' . $systemDescriptions . '</p>
    <p>' . "\(" . $descSymbols . "\)" . '</p>';
        } else {
            $blokovkaLogic .= '
        <p data-translate="EqOdozvaTask">' . $task . '</p>
        <p>' . "\(" . $taskSymbols . "\)" . '</p>';
        }

        $blokovkaLogic .= '
</div>
<div id="solution" style="display: none;">';
        $blokovkaLogic .= '
    <p>' . "\(" . $solutions . "\)" . '</p>';

        $blokovkaLogic .= '
</div>
<button id="toggleSolution" onclick="toggleSolution()" data-translate="solution">Show solution</button>';
    }



    echo $solutions . '<br>'; //ECHOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOO
} else {
    $generateButton = '<button id="togglegeneration" onclick="toggleGeneration()" data-translate="generateEQ">Generate equations</button>';
    $generateInputBox = '<input type="number" id="inputValue">';
}
?>