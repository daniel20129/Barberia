<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "PHP funciona correctamente";

include "conexion.php";

echo "<br>Conexión a MySQL correcta";

$resultado = $conexion->query("SELECT * FROM citas");

if (!$resultado) {
    die("<br>Error SQL: " . $conexion->error);
}

echo "<br>La tabla citas existe. Registros: " . $resultado->num_rows;

?>