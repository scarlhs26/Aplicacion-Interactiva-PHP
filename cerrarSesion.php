<?php

// Iniciar Sesion
session_start();

// Destruir las variables de sesion
session_unset();

// Destruir la sesion
session_destroy();

// Redirigir al index

header("Location: index.php");
exit();

?>