<?php
header("Content-Type: application/json; charset=utf-8");
include "conexion.php";

function responder($success, $mensaje, $id = null, $codigo = 200) {
    http_response_code($codigo);
    $respuesta = ["success" => $success, "mensaje" => $mensaje];
    if ($id !== null) {
        $respuesta["id"] = $id;
    }
    echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
    exit;
}

$datos = json_decode(file_get_contents("php://input"), true);

if (!is_array($datos)) {
    responder(false, "No se recibieron datos válidos.", null, 400);
}

$id = isset($datos["id"]) && $datos["id"] !== "" ? (int)$datos["id"] : 0;
$nombre = trim($datos["nombre"] ?? "");
$telefono = trim($datos["telefono"] ?? "");
$fecha = trim($datos["fecha"] ?? "");
$hora = trim($datos["hora"] ?? "");
$servicio = trim($datos["servicio"] ?? "");
$precio = isset($datos["precio"]) ? (float)$datos["precio"] : 0;
$estado = trim($datos["estado"] ?? "pendiente");
$notas = trim($datos["notas"] ?? "");

if ($nombre === "" || $telefono === "" || $fecha === "" || $hora === "" || $servicio === "") {
    responder(false, "Completa nombre, teléfono, fecha, hora y servicio.", null, 400);
}

$estadosPermitidos = ["pendiente", "confirmado", "atendido", "cancelado"];
if (!in_array($estado, $estadosPermitidos, true)) {
    responder(false, "El estado de la cita no es válido.", null, 400);
}

try {
    $conexion->begin_transaction();

    // Buscar al cliente por teléfono; si ya existe, reutilizarlo.
    $stmtCliente = $conexion->prepare("SELECT id FROM clientes WHERE telefono = ? LIMIT 1");
    $stmtCliente->bind_param("s", $telefono);
    $stmtCliente->execute();
    $resultadoCliente = $stmtCliente->get_result();

    if ($resultadoCliente->num_rows > 0) {
        $filaCliente = $resultadoCliente->fetch_assoc();
        $clienteId = (int)$filaCliente["id"];

        $stmtActualizarCliente = $conexion->prepare("UPDATE clientes SET nombre = ? WHERE id = ?");
        $stmtActualizarCliente->bind_param("si", $nombre, $clienteId);
        $stmtActualizarCliente->execute();
        $stmtActualizarCliente->close();
    } else {
        $stmtNuevoCliente = $conexion->prepare("INSERT INTO clientes (nombre, telefono) VALUES (?, ?)");
        $stmtNuevoCliente->bind_param("ss", $nombre, $telefono);
        $stmtNuevoCliente->execute();
        $clienteId = (int)$conexion->insert_id;
        $stmtNuevoCliente->close();
    }
    $stmtCliente->close();

    if ($id > 0) {
        $stmtCita = $conexion->prepare(
            "UPDATE citas
             SET cliente_id = ?, fecha = ?, hora = ?, servicio = ?, precio = ?, estado = ?, notas = ?
             WHERE id = ?"
        );
        $stmtCita->bind_param(
            "isssdssi",
            $clienteId,
            $fecha,
            $hora,
            $servicio,
            $precio,
            $estado,
            $notas,
            $id
        );
        $stmtCita->execute();

        if ($stmtCita->affected_rows === 0) {
            // Puede ser que la cita exista y los datos no hayan cambiado.
            $stmtVerificar = $conexion->prepare("SELECT id FROM citas WHERE id = ?");
            $stmtVerificar->bind_param("i", $id);
            $stmtVerificar->execute();
            $existe = $stmtVerificar->get_result()->num_rows > 0;
            $stmtVerificar->close();

            if (!$existe) {
                $stmtCita->close();
                $conexion->rollback();
                responder(false, "No se encontró la cita que intentas editar.", null, 404);
            }
        }

        $stmtCita->close();
        $conexion->commit();
        responder(true, "Cita actualizada correctamente.", $id);
    } else {
        $stmtCita = $conexion->prepare(
            "INSERT INTO citas (cliente_id, fecha, hora, servicio, precio, estado, notas)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        $stmtCita->bind_param(
            "isssdss",
            $clienteId,
            $fecha,
            $hora,
            $servicio,
            $precio,
            $estado,
            $notas
        );
        $stmtCita->execute();
        $nuevoId = (int)$conexion->insert_id;
        $stmtCita->close();

        $conexion->commit();
        responder(true, "Cita registrada correctamente.", $nuevoId);
    }
} catch (Throwable $error) {
    $conexion->rollback();
    error_log("Error en guardar_cita.php: " . $error->getMessage());
    responder(false, "Error del servidor al guardar la cita. Revisa el registro de errores de PHP.", null, 500);
}
?>