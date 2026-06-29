<?php

header("Content-Type: application/json");

$conn = new mysqli("localhost","root","","veterinaria");

$data = json_decode(file_get_contents("php://input"), true);

$id = intval($data["id"]);

$sql = "DELETE FROM citas WHERE id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i",$id);

echo json_encode([
    "success"=>$stmt->execute()
]);

$stmt->close();
$conn->close();

?>