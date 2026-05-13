<?php
header("Content-Type: application/json");

$conn = new mysqli("localhost","root","","veterinaria");

if($conn->connect_error){
    die(json_encode([
        "success" => false
    ]));
}

$nombre = $_POST["nombre"];
$tipo = $_POST["tipo"];
$precio = $_POST["precio"];
$stock = $_POST["stock"];
$descripcion = $_POST["descripcion"];

$imagenNombre = "";

if(isset($_FILES["imagen"])){

    $carpeta = "uploads/";

    if(!file_exists($carpeta)){
        mkdir($carpeta, 0777, true);
    }

    $imagenNombre = time() . "_" . $_FILES["imagen"]["name"];

    move_uploaded_file(
        $_FILES["imagen"]["tmp_name"],
        $carpeta . $imagenNombre
    );
}

$sql = "INSERT INTO catalogo
(nombre,tipo,precio,stock,descripcion,imagen)
VALUES
(?,?,?,?,?,?)";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "ssdiss",
    $nombre,
    $tipo,
    $precio,
    $stock,
    $descripcion,
    $imagenNombre
);

$stmt->execute();

echo json_encode([
    "success" => true
]);

?>