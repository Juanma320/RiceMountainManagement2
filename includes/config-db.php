<?php
// Configuración de conexión a la base de datos
$host = 'localhost';
$usuario = 'root';
$contrasena = '';
$base_datos = 'mountaindb';

// Crear conexión
$conexion = mysqli_connect($host, $usuario, $contrasena, $base_datos);

// Verificar la conexión
if (!$conexion) {
    die('Error de conexión: ' . mysqli_connect_error());
}