<?php
$conn = new mysqli("localhost","root","","veterinaria");

if($conn->connect_error){
    die("Error conexión: ".$conn->connect_error);
}

$conn->set_charset("utf8");
?>