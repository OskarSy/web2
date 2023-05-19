<?php
require_once ('config.php');
if (empty($_SESSION["id"])) {
    header("Location: ../index.php");
}

if (session_status() === PHP_SESSION_ACTIVE) {
    session_unset();
    session_destroy();
}
header("Location: ../index.php");