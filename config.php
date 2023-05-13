<?php
$servername = "localhost";
$username = "xsykorao";
$password = "7UsOgRNc28h0CN3";
$dbname = "zadanie1";



session_start(); 
$con = mysqli_connect($servername, $username, $password, $dbname);
if (!$con) {
  die("Connection failed: ");
}




