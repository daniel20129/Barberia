<?php
header("Content-Type: application/json; charset=utf-8");
include "conexion.php";

$sql = "SELECT
            citas.id,
            clientes.nombre,
            clientes.telefono,
            citas.fecha,
            citas.hora,
            citas.servicio,
            citas.precio,
            citas.estado,
            citas.notas,
            citas.fecha_registro AS fechaRegistro
        FROM citas
        INNER JOIN clientes ON citas.cliente_id = clientes.id
        ORDER BY citas.fecha, citas.hora";

$resultado = $conexion->query($sql);

if (!$resultado) {
    http_response_code(500);
    echo json_encode([
        "error" => "Error al consultar las citas."
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$citas = [];
while ($fila = $resultado->fetch_assoc()) {
    $citas[] = $fila;
}

echo json_encode($citas, JSON_UNESCAPED_UNICODE);
$conexion->close();
?>