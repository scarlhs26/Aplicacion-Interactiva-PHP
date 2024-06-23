<?php

// Configuracion base de datos

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "rsocial";

// Crear la conexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Hay que verificar si la conexion fue exitosa
if ($conn->connect_error){
    die("error de conexion: " . $conn->connect_error);
}

?>