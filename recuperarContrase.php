<?php
session_start();
include("config.php"); // Incluye el archivo de configuración de la base de datos

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

// Inicializar las variables de mensaje
$error_message = ""; 
$success_message = "";

// Verificar si se recibió una solicitud POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener el correo del formulario
    $correo_electronico = isset($_POST['correo_electronico']) ? $_POST['correo_electronico'] : '';

    // Validar los datos del formulario
    if (!empty($correo_electronico)) {

        // Verificar si el correo está registrado en la base de datos
        $consulta = "SELECT * FROM usuarios WHERE correo_electronico = ?";
        $stmt = $conn->prepare($consulta);
        $stmt->bind_param("s", $correo_electronico);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            // El correo no está registrado
            $error_message = "Usuario no registrado.";
        } else {
            // El correo está registrado, generar nueva contraseña y enviar correo
            $nueva_contrasena = generarContrasenaAleatoria(); // Generar contraseña aleatoria

            // Actualizar la contraseña en la base de datos
            $fila = $result->fetch_assoc();
            $id_usuario = $fila['id']; // Suponiendo que el ID del usuario está en la columna 'id'

            $consulta_actualizar = "UPDATE usuarios SET contrasena = ? WHERE id = ?";
            $stmt = $conn->prepare($consulta_actualizar);
            $stmt->bind_param("si", $nueva_contrasena, $id_usuario);
            $stmt->execute();

            // Enviar correo con la nueva contraseña
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
                $mail->Subject = 'Recuperación de contraseña';
                $mail->Body = 'Su nueva contraseña es: ' . $nueva_contrasena;

                $mail->send();
                $success_message = "Su correo ha sido enviado.";

                // Redirigir al usuario a la misma página
                header("Location: recuperarContrase.php");
                exit(); // Finalizar el script después de la redirección

            } catch (Exception $e) {
                // En caso de error, muestra el mensaje de error
                $error_message = "Error al enviar su correo.";
            }
        }
    } else {
        // Establecer un mensaje de error si el campo está vacío
        $error_message = "Por favor, complete el campo de correo electrónico.";
    }
}

// Función para generar una contraseña aleatoria
function generarContrasenaAleatoria($longitud = 10) {
    $caracteres = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $longitud_caracteres = strlen($caracteres);
    $contrasena = '';
    for ($i = 0; $i < $longitud; $i++) {
        $contrasena .= $caracteres[rand(0, $longitud_caracteres - 1)];
    }
    return $contrasena;
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
                <h1 style="text-align:center">Recuperar contraseña</h1>
                <p style="text-align:center">Te enviaremos un correo con tu nueva contraseña</p>
            </div>
            <div>
                <form class="form-registro" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <input class="input-svg-mail" type="text" name="correo_electronico" placeholder="Ingrese su correo electrónico">
                    <input type="submit" value="Enviar código">
                </form>
                <div style="height: 20px; width: 280px;">
                    <?php if (!empty($error_message)) { ?>
                        <div style="color:red; font-size: 12px; text-align: center;"><?php echo $error_message; ?></div>
                    <?php } ?>
                    <?php if (!empty($success_message)) { ?>
                        <div style="color:green; font-size: 12px; text-align: center;"><?php echo $success_message; ?></div>
                    <?php } ?>
                </div>
            </div>
            <p class="p-registrate">¿Aún no tienes una cuenta? <a class="a-registrate" href="registro.php">Registrate aqui.</a></p>
            <p class="p-registrate"><a class="a-registrate" href="/rsocial/index.php">⬅ Volver al inicio de sesión</a></p>
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

