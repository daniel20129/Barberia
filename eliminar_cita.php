<?php
header("Content-Type: application/json; charset=utf-8");
include "conexion.php";

$datos = json_decode(file_get_contents("php://input"), true);
$id = isset($datos["id"]) ? (int)$datos["id"] : 0;

if ($id <= 0) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "mensaje" => "El identificador de la cita no es válido."
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$stmt = $conexion->prepare("DELETE FROM citas WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    echo json_encode([
        "success" => true,
        "mensaje" => "Cita eliminada correctamente."
    ], JSON_UNESCAPED_UNICODE);
} else {
    http_response_code(404);
    echo json_encode([
        "success" => false,
        "mensaje" => "No se encontró la cita que intentas eliminar."
    ], JSON_UNESCAPED_UNICODE);
}

$stmt->close();
$conexion->close();
?>