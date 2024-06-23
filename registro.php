<?php
include("config.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

$error_message = ""; // Variable para almacenar los mensajes de error
$success_message = ""; // Variable para almacenar los mensajes de éxito

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recibir datos del formulario de manera segura
    $nombre_usuario = $_POST["nombre_usuario"];
    $correo_electronico = $_POST["correo_electronico"];
    $contraseña = $_POST["contrasena"];
    $rol = "usuario"; // Por defecto, podría ser "usuario" para un nuevo registro

    // Validar los datos del formulario
    if (empty($nombre_usuario) || empty($correo_electronico) || empty($contraseña)) {
        $error_message = "Por favor, completa todos los campos del formulario.";
    } elseif (!filter_var($correo_electronico, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Correo electrónico no válido";
    } elseif (strlen($contraseña) < 8) {
        $error_message = "La contraseña debe tener al menos 8 caracteres.";
    } elseif (!preg_match("/[0-9]/", $contraseña)) {
        $error_message = "La contraseña debe contener al menos un número.";
    } else {
        // Verificar si el nombre de usuario y el correo electrónico ya existen en la base de datos
        $consulta = "SELECT * FROM usuarios WHERE nombre_usuario = ? OR correo_electronico = ?";
        $stmt = $conn->prepare($consulta);
        $stmt->bind_param("ss", $nombre_usuario, $correo_electronico);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error_message = "El nombre de usuario o correo electrónico ya está registrado";
        } else {
            // Insertar el nuevo usuario en la base de datos
            $consulta_insertar = "INSERT INTO usuarios (nombre_usuario, correo_electronico, contrasena, rol) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($consulta_insertar);
            $stmt->bind_param("ssss", $nombre_usuario, $correo_electronico, $contraseña, $rol);

            if ($stmt->execute()) {
                $success_message = "Registro exitoso"; 

                // Envío del correo de bienvenida
                try {
                    $mail = new PHPMailer(true);
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'lycrek@gmail.com';
                    $mail->Password = 'lszgdtziutexyavf';
                    $mail->SMTPSecure = 'ssl';
                    $mail->Port = 465;
                    $mail->setFrom('lycrek@gmail.com');
                    $mail->addAddress($correo_electronico);
                    $mail->isHTML(true);
                    $mail->Subject = 'Bienvenido a nuestro sitio';
                    $mail->Body = '¡Gracias por registrarte en nuestro sitio! Esperamos que disfrutes de tu experiencia.';

                    $mail->send();
                    // Si el correo se envía correctamente, no necesitas hacer nada aquí
                } catch (Exception $e) {
                    // En caso de error, muestra el mensaje de error
                    $error_message .= "Error al enviar el correo de bienvenida: " . $mail->ErrorInfo;
                }
            } else {
                $error_message = "Error al registrar el usuario: " . $conn->error;
            }
        }
    }
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
            <h1>Registrate</h1>
            <p>Registrate y conecta con tus amigos.</p>

        </div>
        <div>
        <form class="form-registro" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <input class="input-svg" type="text" name="nombre_usuario" placeholder="Nombre de usuario">
            <input class="input-svg" type="text" name="correo_electronico" placeholder="Correo electrónico">
            <input class="input-svg-key" type="password" name="contrasena" placeholder="Contraseña">
            <input type="submit" value="Registrarse">
        </form>
        <div style="height: 20px; width: 280px;">
        <?php if (!empty($error_message)) { ?>
            <div id="mensaje" style="color:red; font-size: 12px; text-align: center; height: 20px;"><?php echo $error_message; ?></div>
        <?php } ?>
        <?php if (!empty($success_message)) { ?>
            <div id="mensaje" style="color:green; font-size: 12px; text-align: center; height: 20px;"><?php echo $success_message; ?></div>
        <?php } ?>

        </div>
        <p class="p-registrate">¿Ya tienes una cuenta? <a class="a-registrate" href="/rsocial/index.php">Iniciar Sesión Ahora.</a></p>
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
