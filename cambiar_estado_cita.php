<?php
header("Content-Type: application/json");
include "conexion.php";

$data = json_decode(file_get_contents("php://input"), true);

$id = $data['id'];
$estado = $data['estado'];

$sql = "UPDATE citas SET estado=? WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $estado, $id);

if($stmt->execute()){
    echo json_encode(["success"=>true]);
}else{
    echo json_encode(["success"=>false]);
}
?>