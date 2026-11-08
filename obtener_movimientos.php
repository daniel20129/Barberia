<?php
header("Content-Type: application/json; charset=utf-8");
include "conexion.php";

$sql = "SELECT id, tipo, monto, descripcion, fecha, fecha_registro
        FROM movimientos
        ORDER BY fecha DESC, id DESC";

$resultado = $conexion->query($sql);

if (!$resultado) {
    http_response_code(500);
    echo json_encode([
        "error" => "Error al consultar movimientos: " . $conexion->error
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$movimientos = [];
while ($fila = $resultado->fetch_assoc()) {
    $movimientos[] = $fila;
}

echo json_encode($movimientos, JSON_UNESCAPED_UNICODE);
$conexion->close();
?>