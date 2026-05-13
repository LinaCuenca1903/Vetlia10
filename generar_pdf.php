<?php

require("fpdf/fpdf.php");

$conn = new mysqli(
    "localhost",
    "root",
    "",
    "veterinaria"
);

$id = intval($_GET["id"]);

$sql = "
SELECT c.*, m.nombre mascota
FROM cotizaciones c
JOIN mascotas m
ON c.mascota_id = m.id
WHERE c.id = $id
";

$cot = $conn
->query($sql)
->fetch_assoc();

$pdf = new FPDF();

$pdf->AddPage();


// =======================
// LOGO
// =======================

//$pdf->Image('logo.png',10,10,30);



// =======================
// TITULO
// =======================

$pdf->SetFont('Arial','B',20);

$pdf->SetTextColor(52,152,219);

$pdf->Cell(
    190,
    15,
    utf8_decode('VETLIA'),
    0,
    1,
    'C'
);

$pdf->SetFont('Arial','',12);

$pdf->SetTextColor(100,100,100);

$pdf->Cell(
    190,
    8,
    utf8_decode('Cotización Veterinaria'),
    0,
    1,
    'C'
);

$pdf->Ln(10);


// =======================
// DATOS
// =======================

$pdf->SetTextColor(0,0,0);

$pdf->SetFont('Arial','B',12);

$pdf->Cell(40,10,'Mascota:');

$pdf->SetFont('Arial','',12);

$pdf->Cell(
    100,
    10,
    utf8_decode($cot["mascota"]),
    0,
    1
);

$pdf->SetFont('Arial','B',12);

$pdf->Cell(40,10,'Fecha:');

$pdf->SetFont('Arial','',12);

$pdf->Cell(
    100,
    10,
    $cot["fecha"],
    0,
    1
);

$pdf->SetFont('Arial','B',12);


$pdf->SetFont('Arial','',12);

$pdf->Cell(
    100,
    10,
    utf8_decode($cot["estado"]),
    0,
    1
);

$pdf->Ln(10);


// =======================
// TABLA HEADER
// =======================

$pdf->SetFillColor(52,152,219);

$pdf->SetTextColor(255,255,255);

$pdf->SetFont('Arial','B',12);

$pdf->Cell(70,10,'Item',1,0,'C',true);

$pdf->Cell(30,10,'Cant.',1,0,'C',true);

$pdf->Cell(40,10,'Precio',1,0,'C',true);

$pdf->Cell(50,10,'Subtotal',1,1,'C',true);


// =======================
// ITEMS
// =======================

$pdf->SetTextColor(0,0,0);

$pdf->SetFont('Arial','',11);

$sqlDetalle = "
SELECT d.*, c.nombre
FROM detalle_cotizacion d
JOIN catalogo c
ON d.catalogo_id = c.id
WHERE d.cotizacion_id = $id
";

$rDetalle = $conn->query($sqlDetalle);

while($d = $rDetalle->fetch_assoc()){

    $pdf->Cell(
        70,
        10,
        utf8_decode($d["nombre"]),
        1
    );

    $pdf->Cell(
        30,
        10,
        $d["cantidad"],
        1,
        0,
        'C'
    );

    $pdf->Cell(
        40,
        10,
        '$ '.number_format($d["precio"]),
        1,
        0,
        'C'
    );

    $pdf->Cell(
        50,
        10,
        '$ '.number_format($d["subtotal"]),
        1,
        1,
        'C'
    );
}


// =======================
// TOTAL
// =======================

$pdf->SetFont('Arial','B',14);

$pdf->SetFillColor(230,230,230);

$pdf->Cell(
    140,
    12,
    'TOTAL',
    1,
    0,
    'R',
    true
);

$pdf->Cell(
    50,
    12,
    '$ '.number_format($cot["total"]),
    1,
    1,
    'C',
    true
);

$pdf->Ln(20);


// =======================
// FOOTER
// =======================

$pdf->SetFont('Arial','I',10);

$pdf->SetTextColor(120,120,120);

$pdf->Cell(
    190,
    10,
    utf8_decode(
        'Gracias por confiar en Vetlia'
    ),
    0,
    1,
    'C'
);

$pdf->Output();

?>