<?php
require './config/config.php';
session_start();
if (empty($_SESSION["id"])) {
    header("Location: https://site215.webte.fei.stuba.sk/semestralka/api/login.php");
}
$email=$_SESSION['email'];
$id=$_SESSION['id'];

mysqli_query($conn, $sql);

if (session_status() === PHP_SESSION_ACTIVE) {
    session_unset();
    session_destroy();
}
header("Location: https://site215.webte.fei.stuba.sk/semestralka");