<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "conexion.php";

if ($conexion->connect_error) {
    die("ERROR DE CONEXIÓN: " . $conexion->connect_error);
}

echo "CONEXIÓN OK<br>";

$resultado = $conexion->query("SHOW TABLES");

if (!$resultado) {
    die("ERROR SQL: " . $conexion->error);
}

echo "TABLAS EN LA BD:<br>";
while ($fila = $resultado->fetch_array()) {
    echo "- " . $fila[0] . "<br>";
}
?>