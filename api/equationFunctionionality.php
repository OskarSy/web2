<?php
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
isBlokovka($latexFile);
// Define the regular expressions to match task and solution environments
$taskRegex = '/\\\\begin{task}(.*?)\\\\end{task}/s';
$solutionRegex = '/\\\\begin{solution}(.*?)\\\\end{solution}/s';

// Extract tasks and solutions using regular expressions
$tasks = [];
$taskSymbols = [];
$solutions = [];
$systemDescription = [];
$imagePath = [];
$descSymbols=[];

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


foreach ($tasks as $index => $task) {
    $pattern = '/\\\\dfrac/';
    $replacement = '\\\\frac';
    $equation1 = preg_replace($pattern, $replacement, $task);
    if (!isBlokovka($latexFile)) {
        $pattern = '/(.*?)\sfunkciou\b/';
    preg_match($pattern, $equation1, $matches);
    $equation = $matches[1];
    $buffer=$equation." funkciou";
    $task[$index]=$buffer;
    if(od1($latexFile)){
         forBlokovka1($index,$equation,$tasks,$taskSymbols,$task);
    }
    else{
        forBlokovka2($index, $task, $tasks, $taskSymbols, $descSymbols,$systemDescriptions);
    }

    
    } else {
        $taskSymbols[$index] = createEquation($equation1);
        $tasks[$index] = createTask($equation1, $taskSymbols[$index]);
        $buffer = createDescription($tasks[$index], $equation1);
        $buffer = explode(": ", $buffer);
        $imagePath[$index] = $buffer[1];
        $systemDescriptions[$index] = $buffer[0];
    }

}
foreach ($solutions as $index => $solution) {
    $pattern = '/\\\\dfrac/';
    $replacement = '\\\\frac';
    $equation = preg_replace($pattern, $replacement, $solution);
    $solutions[$index] = $equation;

}


function forBlokovka2($index, $task, &$tasks, &$taskSymbols, &$descSymbols,&$systemDescriptions)
{
    $systemDescriptions[$index] = createTask2($task);

    $descSymbols[$index] = explode($systemDescriptions[$index], $tasks[$index]);
    $descSymbols[$index]=$descSymbols[$index][1];

    $result = explode($descSymbols[$index], $task);
    $pattern = '/(.*?)(\sna.*)/i';
    $result = preg_replace($pattern, '', $result[0]);
    $delimiter = "rovnicou";
    $result = explode($delimiter, $result);
    $tasks[$index] = $result[0];
    $taskSymbols[$index] = $result[1];
}



function forBlokovka1($index,$equation,&$tasks,&$taskSymbols,$task){

    $buffer=$equation." funkciou";
    $taskSymbols[$index] = explode($buffer, $tasks[$index]);
    $taskSymbols[$index]=$taskSymbols[$index][1];
    $tasks[$index]=$buffer;
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
function createTask2($equation1)
{
    $pattern = '/na(.*)/i';
    preg_match($pattern, $equation1, $matches);
    $result = trim($matches[0]);
    $result = str_replace('\\\\', '', $result);
    return $result;
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

function isBlokovka($latexFile){
    $pattern = '/\/([^\/\d]+)\d*pr\.tex$/';
preg_match($pattern, $latexFile, $matches);
$extractedValue = $matches[1];
if($extractedValue=="blokovka"){
    return true;  
}else if($extractedValue=="odozva"){
    return false;
}
else{
    echo("Pozri si cestu k latex file ".$extractedValue);
}
}

function od1($latexFile){
    $pattern = '/\/([^\/\d]+(?:\d+\w*)?)\d*pr\.tex$/';
preg_match($pattern, $latexFile, $matches);
$extractedValue = $matches[1];
if($extractedValue=="odozva01"){
    return true;  
}else if($extractedValue=="odozva02"){
    return false;
}
else{
    echo("Pozri si cestu k latex file ".$extractedValue);
}
}
$i = generate($tasks);
function generate($tasks){
    return rand(0, count($tasks) - 1); 
}

if(isBlokovka($latexFile)){
$blokovkaLogic = '
<div id="task" class="text-center">';
    $blokovkaLogic .= '
    <p data-translate="EqTask">' . $tasks[$i] . '</p>
    <p>' . "\(" . $taskSymbols[$i] . "\)" . '</p>
    <p data-translate="EqDescription">' . $systemDescriptions[$i] . '</p>
    <div class="image-container">
        <img src="' . $imagePath[$i] . '" alt="' . $systemDescriptions[$i] . '" class="img-fluid">
    </div>';
$blokovkaLogic .= '
</div>
<div id="solution" style="display: none;">';
    $blokovkaLogic .= '
    <p>' . "\(" . $solutions[$i] . "\)" . '</p>';
$blokovkaLogic .= '
</div>
<button id="toggleSolution" onclick="toggleSolution()" data-translate="solution">Show solution</button>';
}else{




$blokovkaLogic = '
<div id="task" class="text-center">';
    if(!od1($latexFile)){
    $blokovkaLogic .= '
    <p data-translate="EqOdozva2Task">' . $tasks[$i] . '</p>
    <p>' . "\(" . $taskSymbols[$i] . "\)" . '</p>
    <p data-translate="EqOdozva2Description">' . $systemDescriptions[$i] . '</p>
    <p>' . "\(" . $descSymbols[$i] . "\)" . '</p>';}
    else{
        $blokovkaLogic .= '
        <p data-translate="EqOdozvaTask">' . $tasks[$i] . '</p>
        <p>' . "\(" . $taskSymbols[$i] . "\)" . '</p>';
    }

$blokovkaLogic .= '
</div>
<div id="solution" style="display: none;">';
    $blokovkaLogic .= '
    <p>' . "\(" . $solutions[$i] . "\)" . '</p>';

$blokovkaLogic .= '
</div>
<button id="toggleSolution" onclick="toggleSolution()" data-translate="solution">Show solution</button>';
}?>