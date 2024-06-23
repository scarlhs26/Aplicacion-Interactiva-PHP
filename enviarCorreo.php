<?php

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
    $correo = isset($_POST['correo']) ? $_POST['correo'] : '';

    // Verificar si la dirección de correo electrónico es válida
    if (filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $mail = new PHPMailer(true);
        
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'lycrek@gmail.com';
        $mail->Password = 'lszgdtziutexyavf';
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;
        $mail->setFrom('lycrek@gmail.com');
        $mail->addAddress($correo);
        $mail->isHTML(true);
        $mail->Subject = 'Recuperación de contraseña';
        $mail->Body = 'Se ha enviado un correo de recuperación de contraseña.';

        try {
            // Intenta enviar el correo electrónico
            $mail->send();
            $success_message = "Su correo ha sido enviado.";
        } catch (Exception $e) {
            // En caso de error, muestra el mensaje de error
            $error_message = "Error al enviar su correo.";
        }
    } else {
        // Muestra un mensaje si la dirección de correo electrónico no es válida
        $error_message = "Por favor, introduzca una dirección de correo electrónico válida.";
    }

    // Si no hay mensajes de error, establece un mensaje de éxito
    if (empty($error_message) && empty($success_message)) {
        $success_message = "Su correo ha sido enviado.";
    }

    // Devolver los mensajes como JSON
    echo json_encode(array('error_message' => $error_message, 'success_message' => $success_message));
}
?>
