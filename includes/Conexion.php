<?php
function cargarAmbiente()
{
    $ruta = __DIR__ . '/../.env';

    if (!file_exists($ruta) || !is_readable($ruta)) {
        error_log("Archivo .env no encontrado en: $ruta");
        return;
    }

    $lineas = file($ruta, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lineas as $linea) {
        $linea = trim($linea);

        if (empty($linea) || strpos($linea, '#') === 0) {
            continue;
        }

        if (strpos($linea, '=') !== false) {
            list($nombre, $valor) = explode('=', $linea, 2);

            $nombre = trim($nombre);
            $valor = trim($valor, " \t\n\r\0\x0B\"");

            putenv("$nombre=$valor");
        }
    }
}


function obtenerConexion()
{
    cargarAmbiente();

    $host = getenv('DB_HOST');
    $user = getenv('DB_USER');
    $pass = getenv('DB_PASS');
    $db = getenv('DB_NAME');

    $conexion = mysqli_connect($host, $user, $pass, $db);

    if (!$conexion) {
        die("Error de conexión: " . mysqli_connect_error());
    }

    mysqli_set_charset($conexion, "utf8");
    return $conexion;
}
?>