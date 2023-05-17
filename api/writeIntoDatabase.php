<?php
require_once ('config.php');

$sessionId = $_SESSION["id"] ?? null;

// If the session ID is not available, return an error response
if (!$sessionId) {
    header("HTTP/1.1 401 Unauthorized");
    exit("Unauthorized");
}

    // Read the LaTeX file content
    $filenames = array(
        '../equations/latex/odozva01pr.tex',
        '../equations/latex/odozva02pr.tex',
        '../equations/latex/blokovka01pr.tex',
        '../equations/latex/blokovka02pr.tex'
    );

    $randomIndex = array_rand($filenames);
    $latexFile = $filenames[$randomIndex];

    $latexContent = file_get_contents($latexFile);


    // Define the regular expressions to match task and solution environments
    $taskRegex = '/\\\\begin{task}(.*?)\\\\end{task}/s';
    $solutionRegex = '/\\\\begin{solution}(.*?)\\\\end{solution}/s';

    // Extract tasks and solutions using regular expressions
    $tasks = [];
    $taskSymbols = [];
    $solutions = [];
    $systemDescription = [];
    $imagePath = [];
    $descSymbols = [];

    // Match task environments
    if (preg_match_all($taskRegex, $latexContent, $taskMatches)) {
        $tasks = $taskMatches[1];
    }


    // Match solution environments
    if (preg_match_all($solutionRegex, $latexContent, $solutionMatches)) {
        $solutions = $solutionMatches[1];
    }

    $pattern = '/\\$(.*?)\\$/';

    // Remove $ symbols from tasks
    $tasks = preg_replace($pattern, '$1', $tasks);

    // Remove $ symbols from solutions
    $solutions = preg_replace($pattern, '$1', $solutions);

    // Print updated tasks and solutions
    $pattern = '/\\\begin{equation\*}/';
    $solutions = preg_replace($pattern, '', $solutions);
    $pattern = '/\\\end{equation\*}/';
    $solutions = preg_replace($pattern, '', $solutions);
    $pattern = '/\\\begin{equation\*}/';
    $tasks = preg_replace($pattern, '', $tasks);
    $pattern = '/\\\end{equation\*}/';
    $tasks = preg_replace($pattern, '', $tasks);
    $fulltask = $tasks;
    foreach ($solutions as $index => $solution) {
        $pattern = '/\\\\dfrac/';
        $replacement = '\\\\frac';
        $equation = preg_replace($pattern, $replacement, $solution);
        $solutions[$index] = $equation;

    }

    $i = generate($tasks);
    function generate($tasks)
    {
        return rand(0, count($tasks) - 1);
    }

    $solutions[$i] = str_replace(' ', '', $solutions[$i]);
    $solutions[$i] = preg_replace('/\s/', '', $solutions[$i]);
    $result = $conn->query("SELECT * FROM Assignments");
    $foundDuplicate = false;

    while ($row = $result->fetch_assoc()) {
        $correctAnswer = $row['correctAnswer'];

        if ($correctAnswer == $solutions[$i]) {
            $foundDuplicate = true;
            break;
        }
    }

    if ($foundDuplicate) {
    } else {
        // No duplicate entry found, proceed with the insertion
        $subString = substring($latexFile);
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
        $stmt->bind_param('sssi', $solutions[$i], $fulltask[$i], $pnts, $groupId);
        $stmt->execute();
    }



    function substring($latexFile)
    {
        $pattern = '/\/([^\/\d]+(?:\d+\w*)?)\d*pr\.tex$/';
        preg_match($pattern, $latexFile, $matches);
        $extractedValue = $matches[1];
        return $extractedValue;
    }
echo ("done");
?>