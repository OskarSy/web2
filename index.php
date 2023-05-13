<?php
session_start();

function loadLanguageFile($lang)
{
    // Fallback to default language if the selected language is not available
    $langFile = __DIR__ . "/languages/{$lang}.php";
    if (!file_exists($langFile)) {
        $lang = 'en'; // Set default language
        $langFile = __DIR__ . "/languages/{$lang}.php";
    }

    // Load the language file
    return include $langFile;
}

function translate($key, $args = [])
{
    global $translations;

    // Retrieve the translation
    $translation = $translations[$key] ?? $key;

    // Replace placeholders with provided arguments
    foreach ($args as $argKey => $argValue) {
        $translation = str_replace(":{$argKey}", $argValue, $translation);
    }

    return $translation;
}

// Detect the language based on user preferences or use a default language
$lang = $_SESSION['lang'] ?? $_COOKIE['lang'] ?? $_GET['lang'] ?? 'en';

// Load the language file
$translations = loadLanguageFile($lang);

$name = 'John';


echo translate('welcome');  
echo '<br>';
echo translate('greeting', ['name' => $name]);  

echo '<br>';
echo '<a href="?lang=en">English</a> | <a href="?lang=sk">Slovenčina</a>';

// Handle language switching
if (isset($_GET['lang'])) {
    switchLanguage($_GET['lang']);
}

function switchLanguage($lang)
{
    // Store the selected language in session and cookie
    $_SESSION['lang'] = $lang;
    setcookie('lang', $lang, time() + (365 * 24 * 60 * 60), '/'); // 1 year expiry

    // Reload the page to apply the new language
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}
?>