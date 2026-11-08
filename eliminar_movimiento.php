<?php
header("Content-Type: application/json; charset=utf-8");
include "conexion.php";

$datos = json_decode(file_get_contents("php://input"), true);
$id = isset($datos["id"]) ? (int)$datos["id"] : 0;

if ($id <= 0) {
    http_response_code(400);
    echo json_encode(["success" => false, "mensaje" => "ID inválido."]);
    exit;
}

$stmt = $conexion->prepare("DELETE FROM movimientos WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    echo json_encode(["success" => true, "mensaje" => "Movimiento eliminado."]);
} else {
    http_response_code(404);
    echo json_encode(["success" => false, "mensaje" => "No encontrado."]);
}

$stmt->close();
$conexion->close();
?>