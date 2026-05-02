<?php
header("Content-Type: application/json");

$conn = new mysqli("localhost","root","","veterinaria");

$data = json_decode(file_get_contents("php://input"), true);

$id = $data['id'];
$nombre = $data['nombre'];
$especie = $data['especie'];
$raza = $data['raza'];
$edad = $data['edad'];
$tutor_id = $data['tutor_id'];

$sql = "UPDATE mascotas SET nombre=?, especie=?, raza=?, edad=?, tutor_id=? WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssii",$nombre,$especie,$raza,$edad,$tutor_id,$id);

if($stmt->execute()){
    echo json_encode(["success"=>true]);
}else{
    echo json_encode(["success"=>false]);
}