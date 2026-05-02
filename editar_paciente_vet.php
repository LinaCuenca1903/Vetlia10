<?php
header("Content-Type: application/json");

$conn = new mysqli("localhost","root","","veterinaria");

$data = json_decode(file_get_contents("php://input"), true);

$id = $data['id'];
$nombre = $data['nombre'];
$especie = $data['especie'];
$raza = $data['raza'];
$edad = $data['edad'];

// 🔥 NO TOCAR tutor_id
$sql = "UPDATE mascotas 
SET nombre=?, especie=?, raza=?, edad=? 
WHERE id=?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssi",$nombre,$especie,$raza,$edad,$id);

if($stmt->execute()){
    echo json_encode(["success"=>true]);
}else{
    echo json_encode(["success"=>false,"error"=>$stmt->error]);
}