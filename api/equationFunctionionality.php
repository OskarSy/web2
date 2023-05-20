<?php

function isSubmitted($assignmentId, $generationIndex, $studentId)
{
    require('config.php');
    $stmt = $conn->prepare("SELECT submittedAnswer FROM StudentAssignmentLink WHERE studentId = ? AND assignmentId = ? AND generationIndex = ?");
    $stmt->bind_param('iii', $studentId, $assignmentId, $generationIndex);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        return true;
    } else {
        return false;
    }
}

function getAllGeneratedEquations($studentId, $generationIndex)
{
    require('config.php');
    $stmt = $conn->prepare("SELECT assignmentId, submittedAnswer FROM StudentAssignmentLink WHERE studentId = ? AND generationIndex = ?");
    $stmt->bind_param('ii', $studentId, $generationIndex);
    $stmt->execute();
    $result = $stmt->get_result();
    $oldEquations = null;
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $isSubmitted = $row['submittedAnswer'] ? true : false;
            $oldEquations[] = array('id' => $row['assignmentId'], 'equation' => generateEquation($row['assignmentId'])[0], 'img' => generateEquation($row['assignmentId'])[1], 'isSubmitted' => $isSubmitted);
        }
    }
    return $oldEquations;
}

function writeIntoDatabase()
{
    require('config.php');

    $sessionId = $_SESSION["id"] ?? null;
    if (!$sessionId) {
        http_response_code(401);
        exit("Unauthorized");
    }

    $filenames = array(
        '../equations/latex/odozva01pr.tex',
        '../equations/latex/odozva02pr.tex',
        '../equations/latex/blokovka01pr.tex',
        '../equations/latex/blokovka02pr.tex'
    );

    $taskRegex = '/\\\\begin{task}(.*?)\\\\end{task}/s';
    $solutionRegex = '/\\\\begin{solution}(.*?)\\\\end{solution}/s';

    foreach ($filenames as $index => $file) {

        $latexFile = file_get_contents($file);
        if (preg_match_all($taskRegex, $latexFile, $taskMatches)) {
            $tasks[] = $taskMatches[1];
        }
        if (preg_match_all($solutionRegex, $latexFile, $solutionMatches)) {
            $groupedSolutions[] = $solutionMatches[1];
        }
        $pattern = '/\\$(.*?)\\$/';

        $groupNames[] = $file;
        $tasks[$index] = preg_replace($pattern, '$1', $tasks[$index]);

        $groupedSolutions[$index] = preg_replace($pattern, '$1', $groupedSolutions[$index]);

        $pattern = '/\\\begin{equation\*}/';
        $groupedSolutions[$index] = preg_replace($pattern, '', $groupedSolutions[$index]);
        $pattern = '/\\\end{equation\*}/';
        $groupedSolutions[$index] = preg_replace($pattern, '', $groupedSolutions[$index]);
        $pattern = '/\\\begin{equation\*}/';
        $tasks[$index] = preg_replace($pattern, '', $tasks[$index]);
        $pattern = '/\\\end{equation\*}/';
        $tasks[$index] = preg_replace($pattern, '', $tasks[$index]);
    }

    foreach ($groupedSolutions as $i => $solutions) {
        foreach ($solutions as $j => $solution) {
            $pattern = '/\\\\dfrac/';
            $replacement = '\\\\frac';
            $equation = preg_replace($pattern, $replacement, $solution);
            $solution = preg_replace('/\s/', '', str_replace(' ', '', $equation));
            $stmt = $conn->prepare("SELECT * FROM Assignments WHERE correctAnswer = ?");
            $stmt->bind_param('s', $solution);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows < 1) {
                $subString = substring($groupNames[$i]);
                $maxPnts = count($tasks) * 15;
                $pnts = 15;
                $result = $conn->query("SELECT id FROM AssignmentGroup WHERE name='$subString'");
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $groupId = $row['id'];
                } else {
                    $canBeUsed = true;
                    $stmt = $conn->prepare('INSERT INTO AssignmentGroup (name, maxPoints, canBeUsed) VALUES (?, ?, ?)');
                    $stmt->bind_param('sis', $subString, $maxPnts, $canBeUsed);
                    $stmt->execute();
                    $groupId = $stmt->insert_id;
                }
                $stmt = $conn->prepare('INSERT INTO Assignments (correctAnswer, body, maxPoints, groupId) VALUES (?, ?, ?, ?)');
                $stmt->bind_param('sssi', $solution, $tasks[$i][$j], $pnts, $groupId);
                $stmt->execute();
            }
        }
    }
}
function substring($latexFile)
{
    $pattern = '/\/([^\/\d]+(?:\d+\w*)?)\d*pr\.tex$/';
    preg_match($pattern, $latexFile, $matches);
    $extractedValue = $matches[1];
    return $extractedValue;
}
function generateEquation($id)
{
    require('config.php');
    $task = "";
    $taskSymbols = "";
    $solutions = "";
    $systemDescription = "";
    $imagePath = "";
    $descSymbols = "";  

    $result = $conn->query("SELECT * FROM Assignments WHERE id='$id'");
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

    if (!isBlokovka($fileName)) {
        $pattern = '/(.*?)\srovnicou\b/';
        preg_match($pattern, $task, $matches);
        $equation = $matches[1];
        $buffer = $equation . " rovnicou";
        $task = $buffer;

        if (isOdozva1($fileName)) {
            forOdozva1($equation, $tasks, $taskSymbols, $task);
        } else {
            forOdozva2($task, $tasks, $taskSymbols, $descSymbols, $systemDescriptions);
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
        $blokovkaLogic = '<div id="task" class="text-center my-auto">'
            . '<p data-translate="EqTask">' . $task . '</p><p>' . "\(" . $taskSymbols . "\)"
            . '</p><p data-translate="EqDescription">' . $systemDescriptions
            . '</p>';
        $img = '<div class="image-container"><img src="'
            . $imagePath . '" alt="' . $systemDescriptions
            . '" class="img-fluid"></div>';
    } else {
        $blokovkaLogic = '<div id="task" class="text-center my-auto">';
        if (!isOdozva1($fileName)) {
            $blokovkaLogic .= '
            <p data-translate="EqOdozva2Task">' . $task .
                '</p><p>' . "\(" . $taskSymbols . "\)" .
                '</p><p data-translate="EqOdozva2Description">' . $systemDescriptions .
                '</p><p>' . "\(" . $descSymbols . "\)" . '</p>';
        } else {
            $blokovkaLogic .= '
        <p data-translate="EqOdozvaTask">' . $task . '</p>
        <p>' . "\(" . $taskSymbols . "\)" . '</p>';
        }
    }
    // echo $solutions . '<br>'; //ECHOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOO

    return [$blokovkaLogic, $img ?? null];
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

function forOdozva2($task, &$tasks, &$taskSymbols, &$descSymbols, &$systemDescriptions)
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

function forOdozva1($equation, &$tasks, &$taskSymbols, $task)
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



function isOdozva1($fileName)
{
    if ($fileName == "odozva01") {
        return true;
    } else if ($fileName == "odozva02") {
        return false;
    }
}
