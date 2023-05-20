<?php
require_once ('config.php');
ini_set('display_errors', 1);
error_reporting(E_ALL);

$sessionId = $_SESSION["id"] ?? null;

// If the session ID is not available, return an error response
if (!$sessionId) {
    header("HTTP/1.1 401 Unauthorized");
    exit("Unauthorized");
}

$studentId=$_SESSION["studentId"];

$assignmentId =  $_POST['submittedId'];
$submittedAnswer = $_POST['submittedAnswer']; 

$generationIndex = $_SESSION['generationIndex'];
/*
$stmt = $conn->prepare("SELECT submittedAnswer FROM StudentAssignmentLink WHERE studentId = ? AND assignmentId = ?");
$stmt->bind_param('ss', $studentId, $assignmentId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $submittedAnswer2 = $row['submittedAnswer'];
}
if($submittedAnswer2==null){$notNow=false;}
else if($submittedAnswer=$submittedAnswer2){
    $notNow=true;
}else{
    $notNow=false;
}
*/


$stmt = $conn->prepare("UPDATE StudentAssignmentLink SET submittedAnswer = ? WHERE studentId = ? AND assignmentId = ? and generationIndex=?");
$stmt->bind_param('ssss', $submittedAnswer, $studentId, $assignmentId,$generationIndex);
$stmt->execute();

$stmt = $conn->prepare("UPDATE Student SET submittedCount = submittedCount + 1 WHERE id = ?");
$stmt->bind_param('s', $studentId);
$stmt->execute();



$stmt = $conn->prepare("SELECT correctAnswer,maxPoints FROM Assignments WHERE id = ?");
$stmt->bind_param('s',$assignmentId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $correctAnswer = $row['correctAnswer'];
    $maxPoints=$row['maxPoints'];
}



// Regex to transform latex syntax to octave symbolic syntax
function latexToOctave($latex) {
    $step1 = preg_replace('/\\\frac\{([^\}]+)\}\{([^\}]+)\}/', '(($1)/($2))', $latex);
    $step2 = preg_replace('/([0-9]+)([a-z])/', '$1*$2', $step1);
    $step3 = preg_replace('/e\^\{(-?[^}]+)\}/', '*eps($1)', $step2);
    $step4 = preg_replace('/\*\*/', '*', $step3);
    return $step4;
}

// Function to check whether two equations are the same using octave symbolic
function checkEquations($equation1, $equation2) {
    $values = "linspace(-10, 10, 10)";

    $octaveScript = <<<OCT
    pkg load symbolic
    syms s t

    f1 = sym("$equation1");
    f2 = sym("$equation2");

    s = $values;
    t = $values;

    result = isequal(eval(f1), eval(f2));
    disp(result);
    OCT;

    $tempFile = tempnam(sys_get_temp_dir(), 'octave_');
    file_put_contents($tempFile, $octaveScript);

    $output = shell_exec("octave $tempFile");
    return $output;
}


$split = explode('=', $correctAnswer);
$equals = 0;

if (count($split) == 1) {
    $equeation = latexToOctave($split[0]);

    $res = checkEquations($equeation, $submittedAnswer);

    if (strpos($res, '0') !== true)  {
        $equals = 1;
    } 
} else {
    $equeation1 = latexToOctave($split[1]);
    $equeation2 = latexToOctave($split[2]);

    $res1 = checkEquations($equeation1, $submittedAnswer);
    $res1 = checkEquations($equeation2, $submittedAnswer);

    if (strpos($res1, '0') !== true || strpos($res2, '0') !== true)  {
        $equals = 1;
    }
}



if($equals == 1){
    $stmt = $conn->prepare("UPDATE StudentAssignmentLink SET achievedPoints = ? WHERE assignmentId = ? and studentId = ? and generationIndex=?");
    $stmt->bind_param('ssss',$maxPoints,$assignmentId, $studentId,$generationIndex);
    $stmt->execute();
}

$stmt = $conn->prepare("SELECT achievedPoints FROM StudentAssignmentLink WHERE studentId = ? AND generationIndex = ?");
$stmt->bind_param('ss', $studentId, $generationIndex);
$stmt->execute();
$result = $stmt->get_result();

$totalAchievedPoints = 0; 

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $achievedPoints = $row['achievedPoints'];

        if ($achievedPoints !== null) {
            $totalAchievedPoints += $achievedPoints;
        }
    }
}

$stmt = $conn->prepare("UPDATE Student SET points=? WHERE id = ?");
$stmt->bind_param('ss', $totalAchievedPoints,$studentId);
$stmt->execute();


?>
