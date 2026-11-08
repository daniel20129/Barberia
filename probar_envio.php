<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Prueba de Envío a la Base de Datos</title>
</head>
<body>
    <h2>Dando de alta una cita de prueba...</h2>
    <div id="respuesta">Cargando...</div>

    <script>
        // Datos falsos para ver si se guardan en tu MySQL
        const citaFalsa = {
            nombre: "Juan Perez",
            telefono: "7221234567",
            fecha: "2026-10-10",
            hora: "10:00:00",
            servicio: "Corte de cabello",
            precio: 150.00,
            estado: "pendiente",
            notas: "Corte con diseño moderno"
        };

        // Simulamos lo que hará tu app.js real enviando los datos a guardar_cita.php
        fetch('guardar_cita.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(citaFalsa)
        })
        .then(res => res.json())
        .then(data => {
            console.log(data);
            if(data.success) {
                document.getElementById('respuesta').innerHTML = `<b style="color:green;">¡ÉXITO! ${data.mensaje}. ID de cita: ${data.id}</b>`;
            } else {
                document.getElementById('respuesta').innerHTML = `<b style="color:red;">ERROR: ${data.mensaje}</b>`;
            }
        })
        .catch(err => {
            document.getElementById('respuesta').innerHTML = `<b style="color:red;">Error de conexión con el servidor: ${err}</b>`;
        });
    </script>
</body>
</html>
