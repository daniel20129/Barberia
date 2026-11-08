<?php
header("Content-Type: application/json; charset=utf-8");
include "conexion.php";

$datos = json_decode(file_get_contents("php://input"), true);

if (!is_array($datos)) {
    http_response_code(400);
    echo json_encode(["success" => false, "mensaje" => "Datos inválidos."]);
    exit;
}

$tipo = trim($datos["tipo"] ?? "");
$monto = isset($datos["monto"]) ? (float)$datos["monto"] : 0;
$descripcion = trim($datos["descripcion"] ?? "");
$fecha = trim($datos["fecha"] ?? "");

if (!in_array($tipo, ["entrada", "salida"], true)) {
    http_response_code(400);
    echo json_encode(["success" => false, "mensaje" => "Tipo inválido."]);
    exit;
}

if ($monto <= 0 || $descripcion === "" || $fecha === "") {
    http_response_code(400);
    echo json_encode(["success" => false, "mensaje" => "Completa monto, descripción y fecha."]);
    exit;
}

$stmt = $conexion->prepare(
    "INSERT INTO movimientos (tipo, monto, descripcion, fecha) VALUES (?, ?, ?, ?)"
);
$stmt->bind_param("sdss", $tipo, $monto, $descripcion, $fecha);

if ($stmt->execute()) {
    echo json_encode([
        "success" => true,
        "mensaje" => "Movimiento registrado.",
        "id" => $conexion->insert_id
    ], JSON_UNESCAPED_UNICODE);
} else {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "mensaje" => "Error al guardar: " . $conexion->error
    ]);
}

$stmt->close();
$conexion->close();
?>