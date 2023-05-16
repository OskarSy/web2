<?php



$hostname = "localhost";
$username = "xsobotaj";
$password = "RlSKDocLdZqpBj4";
$dbname = "semestralka";
session_start(); 
$conn = mysqli_connect($hostname, $username, $password, $dbname);
if (!$conn) {
  die("Connection failed: ");
}
if ($conn->connect_error) {
  die('Connect Error (' . $conn->connect_errno . ') '
          . $conn->connect_error);
}


?>