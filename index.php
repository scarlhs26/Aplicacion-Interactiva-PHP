<?php
session_start();
include("config.php"); // Incluye el archivo de configuración de la base de datos

// Verificar si hay una sesión activa
if (!empty($_SESSION["id"])) {
    header("Location: dashboard.php");
    exit(); // Finalizamos el script después de redirigir
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos del formulario de manera segura usando POST
    $nombre_usuario = $_POST["nombre_usuario"];
    $contrasena = $_POST["contrasena"];

    // Vamos a realizar una consulta SQL preparada para evitar inyección de SQL
    $stmt = $conn->prepare("SELECT id, nombre_usuario, contrasena FROM usuarios WHERE nombre_usuario = ?");
    $stmt->bind_param("s", $nombre_usuario);
    $stmt->execute();

    // Enlazar variables de resultados
    $stmt->bind_result($id, $nombre_usuario_db, $contrasena_db);
    $stmt->fetch();

    // Verificar si se encontró un usuario con el nombre de usuario proporcionado
    if ($nombre_usuario_db) {
        // Verificar la contraseña (aquí sería mejor usar una función de hash segura)
        if ($contrasena === $contrasena_db) {
            // Iniciar sesión y redirigir al dashboard
            $_SESSION["id"] = $id;
            $_SESSION["nombre_usuario"] = $nombre_usuario_db;
            header("Location: dashboard.php");
            exit();
        } else {
            $error_message = "Contraseña incorrecta";
        }
    } else {
        $error_message = "No se encontró ningún usuario con ese nombre";
    }

    // Cerrar la consulta
    $stmt->close();
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="styles/main.css">
    <link rel="stylesheet" type="text/css" href="styles/register.css">
    <title>Document</title>

</head>
<body>

    
    <section class="section-registro">
        <div class="div-registro">
        <div class="div-logo">
            <img style="with:50px; height:50px" src="assets/logo.png" alt="logo">
            <h1>Inicio de Sesión</h1>
            <p>Conocenos conecta con el resto.</p>

        </div>
        <div>
        <form class="form-registro" action="index.php" method="post">
            <input class="input-svg" type="text" name="nombre_usuario" placeholder="Nombre de usuario">
            <input class="input-svg-key" type="password" name="contrasena" placeholder="Contraseña">
            <a href="recuperarContrase.php">¿Olvidaste tu contraseña?</a>
            <input type="submit" value="Iniciar Session">
        </form>
        <div style="height: 20px; width: 280px;">
        <?php if (!empty($error_message)) { ?>
            <div id="mensaje" style="color:red; font-size: 12px; text-align: center; height: 20px;"><?php echo $error_message; ?></div>
        <?php } ?>


        </div>

            <p class="p-registrate">¿Aún no tienes una cuenta? <a class="a-registrate" href="registro.php">Registrate aqui.</a></p>

        </div>
        </div>
    </section>


    <script>
        // Función para ocultar el mensaje después de 10 segundos
        function ocultarMensaje() {
            var mensaje = document.getElementById("mensaje");
            if (mensaje) {
                setTimeout(function() {
                    mensaje.style.display = "none";
                }, 10000); // 10 segundos
            }
        }
        // Llamar a la función de ocultarMensaje cuando el documento se haya cargado
        document.addEventListener("DOMContentLoaded", ocultarMensaje);
    </script>
    
</body>
</html>