<?php
include("config.php"); // Asegúrate de tener tu conexión a la base de datos aquí
session_start();

$id_publicacion = isset($_GET['id_publicacion']) ? $_GET['id_publicacion'] : exit('ID de publicación no proporcionado');

// Preparar la consulta para obtener los detalles de la publicación y del usuario dueño
$sql = "SELECT p.*, u.nombre AS nombre_usuario, u.correo_electronico AS correo, u.rol AS rol_usuario 
        FROM publicaciones p 
        INNER JOIN usuarios u ON p.id_usuario = u.id 
        WHERE p.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_publicacion);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $esDueno = ($_SESSION['id'] == $row['id_usuario']);
    $row['esDueno'] = $esDueno; // Ejemplo de añadir más datos al resultado
    // Devuelve los detalles de la publicación y del usuario en formato JSON
    echo json_encode($row);
} else {
    echo json_encode(['error' => 'Publicación no encontrada']);
}

$stmt->close();
?>