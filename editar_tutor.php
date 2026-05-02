<?php
header("Content-Type: application/json");

$conn = new mysqli("localhost","root","","veterinaria");

$data = json_decode(file_get_contents("php://input"), true);

$id = $data['id'];
$nombre = $data['nombre'];
$telefono = $data['telefono'];
$direccion = $data['direccion'];
$email = $data['email'];

$sql = "UPDATE tutores SET nombre=?, telefono=?, direccion=?, email=? WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssi",$nombre,$telefono,$direccion,$email,$id);

if($stmt->execute()){
    echo json_encode(["success"=>true]);
}else{
    echo json_encode(["success"=>false,"error"=>$conn->error]);
}