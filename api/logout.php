<?php
require 'config.php';
if (empty($_SESSION["id"])) {
    header("Location: https://site215.webte.fei.stuba.sk/semestralka");
}

if (session_status() === PHP_SESSION_ACTIVE) {
    session_unset();
    session_destroy();
}
header("Location: https://site215.webte.fei.stuba.sk/semestralka");