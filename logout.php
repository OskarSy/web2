<?php
require '../config.php';
$u=$_SESSION['email'];
$c=$_SESSION['id'];
if (empty($_SESSION["id"])) {
    header("Location: https://site221.webte.fei.stuba.sk/index.php");
}
$sql = "UPDATE User
SET verified='0'
WHERE id='$c'";
    mysqli_query($con, $sql);
$_SESSION = [];
session_unset();
session_destroy();
header("Location: https://site221.webte.fei.stuba.sk/index.php?en=1&username=$u&cid=$c");