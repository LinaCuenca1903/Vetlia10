<?php
header("Content-Type: application/json");

$conn = new mysqli("localhost","root","","veterinaria");

$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'];

$sql = "DELETE FROM tutores WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i",$id);

if($stmt->execute()){
    echo json_encode(["success"=>true]);
}else{
    echo json_encode(["success"=>false]);
}