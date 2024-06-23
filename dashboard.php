<?php
include("config.php");

session_start();
if(!empty($_SESSION["id"])){
    $id = $_SESSION["id"];

    // Utilizar una sentencia preparada para verificar el usuario
    $stmt = $conn->prepare("SELECT id, correo_electronico, contrasena, nombre_usuario, rol FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    // Enlazar variables de resultados
    $stmt->bind_result($id, $correo_electronico, $contraseña, $nombre_usuario, $rol);
    $stmt->fetch();

    // Verificar si se recuperaron los datos del usuario
    if(!$correo_electronico || !$contraseña){
        echo "Error al obtener los datos del usuario";
    }
} else {
    header("Location: index.php");
    exit();
}

// Cerrar la consulta preparada para liberar recursos
$stmt->close();

// Obtener los nombres de usuarios de la base de datos
$usuarios = array();
$stmtUsuarios = $conn->prepare("SELECT nombre_usuario FROM usuarios");
$stmtUsuarios->execute();
$resultUsuarios = $stmtUsuarios->get_result();

if ($resultUsuarios->num_rows > 0) {
    while($row = $resultUsuarios->fetch_assoc()) {
        $usuarios[] = $row["nombre_usuario"];
    }
}
$stmtUsuarios->close();


function obtenerIdUsuario($nombreUsuario) {
    global $conn;
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE nombre_usuario = ?");
    $stmt->bind_param("s", $nombreUsuario);
    $stmt->execute();
    $stmt->bind_result($id);
    $stmt->fetch();
    $stmt->close();
    return $id;
}


function obtenerCorreoUsuario($nombreUsuario) {
    global $conn;
    $stmt = $conn->prepare("SELECT correo_electronico FROM usuarios WHERE nombre_usuario = ?");
    $stmt->bind_param("s", $nombreUsuario);
    $stmt->execute();
    $stmt->bind_result($correo);
    $stmt->fetch();
    $stmt->close();
    return $correo;
}

function obtenerRolUsuario($nombreUsuario) {
    global $conn;
    $stmt = $conn->prepare("SELECT rol FROM usuarios WHERE nombre_usuario = ?");
    $stmt->bind_param("s", $nombreUsuario);
    $stmt->execute();
    $stmt->bind_result($rol);
    $stmt->fetch();
    $stmt->close();
    return $rol;
}

// Verificar si se ha enviado un formulario para crear una nueva publicación
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['publicar'])) {
    $contenido = $_POST['contenido'];
    $id_usuario = $_SESSION['id']; 

    $sql = "INSERT INTO publicaciones (id_usuario, contenido) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("is", $id_usuario, $contenido);
        
        if ($stmt->execute()) {
       
            header("Location: dashboard.php");
            exit();
        } else {
            $error_message = "Error al crear la publicación: " . $stmt->error;
        }
        
        $stmt->close();
    } else {
        $error_message = "Error al preparar la consulta: " . $conn->error;
    }
}

// Obtener todas las publicaciones de la base de datos
$sql = "SELECT publicaciones.*, usuarios.nombre_usuario, publicaciones.id_usuario AS id_usuario_publicacion
FROM publicaciones 
INNER JOIN usuarios ON publicaciones.id_usuario = usuarios.id 
ORDER BY publicaciones.fecha_creacion DESC";

$result = $conn->query($sql);

// Verificar si se ha enviado un formulario para crear un nuevo comentario
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['comentar'])) {
    // Obtener los datos del formulario
    $id_publicacion = $_POST['id_publicacion'];
    $contenido_comentario = $_POST['contenido_comentario'];
    $id_usuario = $_SESSION['id']; // Suponiendo que tienes una sesión de usuario activa

    // Insertar el nuevo comentario en la base de datos
    $stmt = $conn->prepare("INSERT INTO comentarios (id_publicacion, id_usuario, contenido) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $id_publicacion, $id_usuario, $contenido_comentario);
    
    if ($stmt->execute()) {
        // Comentario insertado correctamente
        header("Location: ".$_SERVER['HTTP_REFERER']); // Redireccionar a la página de origen
        exit();
    } else {
        // Error al insertar el comentario
        echo "Error al procesar el comentario: " . $conn->error;
    }

    $stmt->close();
} else {
    // Si no se enviaron los datos del formulario correctamente, redireccionar a una página de error o mostrar un mensaje de error
}
//------------------------------------------------------------------------------------------------------------------------------------
// Inicializar las variables de mensajes
$error_message = '';
$success_message = '';

// Verificar si se ha enviado un formulario para actualizar la información del usuario o eliminar la cuenta
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['actualizar_usuario'])) {
        // Procesar la actualización del nombre de usuario
        $nuevo_usuario = $_POST['nuevo_usuario'];
        if (!empty($nuevo_usuario)) {
            // Actualizar el nombre de usuario en la base de datos
            $stmt = $conn->prepare("UPDATE usuarios SET nombre_usuario = ? WHERE id = ?");
            $stmt->bind_param("si", $nuevo_usuario, $_SESSION['id']);
            if ($stmt->execute()) {
                // Cerrar sesión después de la actualización
                session_unset();
                session_destroy();
                // Redireccionar al usuario a la página de inicio de sesión
                header("Location: index.php");
                exit();
            } else {
                $error_message = "Error al actualizar el nombre de usuario: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error_message = "Por favor, ingrese un nuevo nombre de usuario.";
        }
    } elseif (isset($_POST['actualizar_correo'])) {
        // Procesar la actualización del correo electrónico
        $nuevo_correo = $_POST['nuevo_correo'];
        if (!empty($nuevo_correo)) {
            // Actualizar el correo electrónico en la base de datos
            $stmt = $conn->prepare("UPDATE usuarios SET correo_electronico = ? WHERE id = ?");
            $stmt->bind_param("si", $nuevo_correo, $_SESSION['id']);
            if ($stmt->execute()) {
                // Cerrar sesión después de la actualización
                session_unset();
                session_destroy();
                // Redireccionar al usuario a la página de inicio de sesión
                header("Location: index.php");
                exit();
            } else {
                $error_message = "Error al actualizar el correo electrónico: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error_message = "Por favor, ingrese un nuevo correo electrónico.";
        }
    } elseif (isset($_POST['actualizar_contrasena'])) {
        // Procesar la actualización de la contraseña
        $nueva_contrasena = $_POST['nueva_contrasena'];
        $confirmar_contrasena = $_POST['confirmar_contrasena'];
        if (!empty($nueva_contrasena) && !empty($confirmar_contrasena)) {
            if ($nueva_contrasena === $confirmar_contrasena) {
                // Actualizar la contraseña en la base de datos
                $stmt = $conn->prepare("UPDATE usuarios SET contrasena = ? WHERE id = ?");
                $stmt->bind_param("si", $nueva_contrasena, $_SESSION['id']);
                if ($stmt->execute()) {
                    // Cerrar sesión después de la actualización
                    session_unset();
                    session_destroy();
                    // Redireccionar al usuario a la página de inicio de sesión
                    header("Location: index.php");
                    exit();
                } else {
                    $error_message = "Error al actualizar la contraseña: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $error_message = "Las contraseñas no coinciden.";
            }
        } else {
            $error_message = "Por favor, ingrese una nueva contraseña y confírmela.";
        }
    }
    elseif (isset($_POST['editar_publicacion'])) {
        // Procesar la edición de la publicación
        $id_publicacion = $_POST['id_publicacion'];
        $nuevo_contenido = $_POST['nuevo_contenido'];
        
        if (!empty($id_publicacion) && !empty($nuevo_contenido)) {
            // Actualizar el contenido de la publicación en la base de datos
            $stmt = $conn->prepare("UPDATE publicaciones SET contenido = ? WHERE id = ?");
            $stmt->bind_param("si", $nuevo_contenido, $id_publicacion);
            if ($stmt->execute()) {
                // Redireccionar al usuario a la página de la publicación editada
                header("Location: dashboard.php");
                exit();
            } else {
                $error_message = "Error al editar la publicación: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error_message = "Por favor, ingrese un nuevo contenido para la publicación.";
        }
    }
    
    elseif (isset($_POST['eliminar_publicacion'])) {
        // Procesar la eliminación de la publicación y los comentarios asociados
        $id_publicacion = $_POST['id_publicacion'];
        
        // Eliminar los comentarios asociados a la publicación
        $stmt_delete_comentarios = $conn->prepare("DELETE FROM comentarios WHERE id_publicacion = ?");
        $stmt_delete_comentarios->bind_param("i", $id_publicacion);
        $stmt_delete_comentarios->execute();
        $stmt_delete_comentarios->close();
        
        // Luego eliminar la publicación
        $stmt_eliminar_publicacion = $conn->prepare("DELETE FROM publicaciones WHERE id = ?");
        $stmt_eliminar_publicacion->bind_param("i", $id_publicacion);
        
        if ($stmt_eliminar_publicacion->execute()) {
            // La publicación y los comentarios asociados se eliminaron correctamente
            // Redirigir o mostrar un mensaje de éxito
            header("Location: {$_SERVER['PHP_SELF']}");
        exit();
        } else {
            // Error al eliminar la publicación
            // Manejar el error adecuadamente
        }
        
        $stmt_eliminar_publicacion->close();
    }
    elseif (isset($_POST['editar_comentario'])) {
        // Procesar la edición del comentario
        $id_comentario = $_POST['id_comentario'];
        $nuevo_contenido = $_POST['nuevo_contenido'];
    
        if (!empty($id_comentario) && !empty($nuevo_contenido)) {
            // Actualizar el contenido del comentario en la base de datos
            $stmt = $conn->prepare("UPDATE comentarios SET contenido = ? WHERE id = ?");
            $stmt->bind_param("si", $nuevo_contenido, $id_comentario);
            if ($stmt->execute()) {
                // Redireccionar al usuario a la página de la publicación o recargar la página actual
                header("Location: {$_SERVER['PHP_SELF']}");
                exit();
            } else {
                $error_message = "Error al editar el comentario: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error_message = "Por favor, ingrese un nuevo contenido para el comentario.";
        }
    }
    
    elseif (isset($_POST['eliminar_comentario'])) {
        // Procesar la eliminación del comentario
        $id_comentario = $_POST['id_comentario'];
    
        // Eliminar el comentario de la base de datos
        $stmt = $conn->prepare("DELETE FROM comentarios WHERE id = ?");
        $stmt->bind_param("i", $id_comentario);
    
        if ($stmt->execute()) {
            // El comentario se eliminó correctamente
            // Redirigir o recargar la página actual
            header("Location: {$_SERVER['PHP_SELF']}");
            exit();
        } else {
            // Error al eliminar el comentario
            // Manejar el error adecuadamente
        }
    
        $stmt->close();
    }
    
}
// Verificar si se ha enviado un formulario para eliminar una cuenta de usuario
if (isset($_POST['eliminar_usuario'])) {
    // Verificar si se proporcionó un ID de usuario a eliminar
    if (isset($_POST['id_usuario_a_eliminar'])) {
        $id_usuario_a_eliminar = $_POST['id_usuario_a_eliminar'];

        // Eliminar comentarios relacionados con las publicaciones del usuario
        $stmt = $conn->prepare("DELETE comentarios FROM comentarios INNER JOIN publicaciones ON comentarios.id_publicacion = publicaciones.id WHERE publicaciones.id_usuario = ?");
        $stmt->bind_param("i", $id_usuario_a_eliminar);
        $stmt->execute();
        $stmt->close();

        // Eliminar publicaciones del usuario
        $stmt = $conn->prepare("DELETE FROM publicaciones WHERE id_usuario = ?");
        $stmt->bind_param("i", $id_usuario_a_eliminar);
        $stmt->execute();
        $stmt->close();

        // Eliminar al usuario
        $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
        $stmt->bind_param("i", $id_usuario_a_eliminar);
        if ($stmt->execute()) {
            // Redireccionar al usuario a alguna página o mostrar un mensaje de éxito
            header("Location: dashboard.php");
            exit();
        } else {
            $error_message = "Error al eliminar la cuenta: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $error_message = "No se proporcionó un ID de usuario para eliminar.";
    }
}



if (isset($_POST['eliminar_cuenta'])) {
    // Eliminar comentarios del usuario
    $stmt = $conn->prepare("DELETE FROM comentarios WHERE id_usuario = ?");
    $stmt->bind_param("i", $_SESSION['id']);
    $stmt->execute();
    $stmt->close();

    // Eliminar publicaciones del usuario
    $stmt = $conn->prepare("DELETE FROM publicaciones WHERE id_usuario = ?");
    $stmt->bind_param("i", $_SESSION['id']);
    $stmt->execute();
    $stmt->close();

    // Eliminar al usuario
    $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['id']);
    if ($stmt->execute()) {
        // Cerrar sesión después de la eliminación
        session_unset();
        session_destroy();
        // Redireccionar al usuario a la página de inicio de sesión
        header("Location: index.php");
        exit();
    } else {
        $error_message = "Error al eliminar la cuenta: " . $stmt->error;
    }
    $stmt->close();
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="styles/main.css">
    <link rel="stylesheet" type="text/css" href="styles/dashboard.css">
    <title>Document</title>
</head>
<body class="body-dashboard">
    <section class="seccion-superior">
        <div>
            <img style="width:50px; height:50px" src="assets/logo.png" alt="">
        </div>
        <h1>Buen día <?php echo $nombre_usuario ?><br>
</h1>


        <div class="iconos-superior">
            <img id="btnMostrar1" class="iconos-bg" style="width:40px; height:40px" src="assets/user.png" alt="">
            <img id="btnMostrar-congf" class="iconos-bg" style="width:40px; height:40px" src="assets/edit.png" alt="">
            <a  href="cerrarSesion.php"><img src="assets/out.svg" alt="Cerrar Session"></a>
        </div>
    </section>

    <!-- Ventana emergente -->
    <section>
        <div id="ventanaEmergente" class="ventana-emergente">
            <div class="contenido">
                <span class="cerrar" id="btnCerrar">&times;</span>
                <h2>Perfil de usuario</h2>
                <div id="nombreUsuario"></div>
                <div id="correoUsuario"></div>
                <div id="rolUsuario"></div>


                <form id="formEliminarUsuario" method="POST" action="">
    <!-- Input oculto para almacenar el ID del usuario a eliminar -->
    <input type="hidden" id="idUsuarioEliminar" name="id_usuario_a_eliminar" value="12">
    <?php if ($rol === 'admin') : ?>
        <button onclick="eliminarCuenta()" type="submit" name="eliminar_usuario" >Eliminar Cuenta</button>
    <?php endif; ?>
</form>


            </div>

        </div>

        <div id="ventanaEmergente2" class="ventana-emergente">
            <div class="contenido">
                <span class="cerrar" id="btnCerrar-user">&times;</span>
                <h2>Perfil de <?php echo $nombre_usuario ?></h2>
                <div id="nombreUsuario">Nombre del usuario: <?php echo $nombre_usuario ?></div>
                <div id="correoUsuario">Correo del usuario: <?php echo $correo_electronico ?></div>
                <div id="rolUsuario">Rol del usuario: <?php echo $rol ?></div>
            </div>
        </div>

        <div id="ventanaEmergente-config" class="ventana-emergente">
    <div class="contenido">
        <span class="cerrar" id="btnCerrar-config">&times;</span>
        <h2>Configuración de usuario</h2>
        <a href="#" class="cambio-opcion" data-opcion="usuario">Cambiar usuario</a><br>
        <a href="#" class="cambio-opcion" data-opcion="correo">Cambiar correo</a><br>
        <a href="#" class="cambio-opcion" data-opcion="contrasena">Cambiar contraseña</a><br>
        <form id="form-eliminar-cuenta" method="POST" action="">
            <button type="submit" name="eliminar_cuenta">Eliminar cuenta</button>
        </form>

        <div id="cambio-usuario" style="display:none;">
            <form method="POST" action="">
                <label for="nuevo_usuario">Nuevo nombre de usuario:</label>
                <input type="text" name="nuevo_usuario" id="nuevo_usuario" placeholder="Nuevo nombre de usuario">
                <button type="submit" name="actualizar_usuario">Actualizar usuario</button>
            </form>
        </div>
        <div id="cambio-correo" style="display:none;">
            <form method="POST" action="">
                <label for="nuevo_correo">Nuevo correo electrónico:</label>
                <input type="email" name="nuevo_correo" id="nuevo_correo" placeholder="Nuevo correo electrónico">
                <button type="submit" name="actualizar_correo">Actualizar correo</button>
            </form>
        </div>
        <div id="cambio-contrasena" style="display:none;">
            <form method="POST" action="">
                <label for="nueva_contrasena">Nueva contraseña:</label>
                <input type="password" name="nueva_contrasena" id="nueva_contrasena" placeholder="Nueva contraseña">
                <label for="confirmar_contrasena">Confirmar nueva contraseña:</label>
                <input type="password" name="confirmar_contrasena" id="confirmar_contrasena" placeholder="Confirmar contraseña">
                <button type="submit" name="actualizar_contrasena">Actualizar contraseña</button>
            </form>
        </div>
        <?php if (!empty($error_message)) { ?>
            <div id="mensaje" style="color:red; font-size: 12px; text-align: center; height: 20px;"><?php echo $error_message; ?></div>
        <?php } ?>
        <?php if (!empty($success_message)) { ?>
            <div id="mensaje" style="color:green; font-size: 12px; text-align: center; height: 20px;"><?php echo $success_message; ?></div>
        <?php } ?>
    </div>
</div>








    </section>

        <!-- -------------------------------------------------->
    </section>

    <section class="seccion-central">
        <div class="sec2">
            <div class="seccion-izq">
                <div id="btnMostrar2" class="seccion-izq-users">
                    <img style="width:40px; height:40px" src="assets/user.png" alt="">
                    <h3><?php echo $nombre_usuario ?></h3>
                </div>
                <div class="hr"></div>
            </div>
        </div>
        
        <div class="seccion-centro">
            <div class="do-publi">
                <form method="POST" action="">
                    <div class="publi-input">
                        <img style="width:40px; height:40px" src="assets/user.png" alt="">
                        <textarea class="input-publicacion" name="contenido" id="inputTexto" type="text" placeholder="¿Qué estás pensando?"></textarea>
                    </div>
                    <div class="publi-btn">
                        <button type="submit" name="publicar">Publicar</button>
                    </div>
                </form>

        </div>
                <?php 
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) { ?>
                        <div class="publicacion">
                            <div class="pub-user-info">
                                <div class="pub-user-info-img">
                                    <img style="width:60px; height:60px" src="assets/user.png" alt="">
                                    <div>
                                        <h4><?php echo $row['nombre_usuario']; ?></h4>
                                        <p><?php echo $row['fecha_creacion']; ?></p>
                                    </div>
                                </div>
                                <?php
                                if ($rol === 'admin') {
                                    $estadoUsuario = 'admin';
                                } elseif (!empty($row['id_usuario']) && $_SESSION["id"] == $row['id_usuario_publicacion']) {
                                    $estadoUsuario = 'true';
                                } else {
                                    $estadoUsuario = 'false';
                                }
                                echo '<img class="ventana-publicacion" src="assets/menu.svg" alt="" data-own="' . $estadoUsuario . '">';
?>                                </div>
                            <div class="pub-user-info-text">
                                <p><?php echo $row['contenido']; ?></p>
                            </div>
                            <img src="assets/imagen.png" alt="">
                            <div class="hr"></div>
                            <div>
                                <p>likes</p>
                            </div>
                            <div class="hr"></div>
                            <div class="pub-coment">
                                <form class="pub-coment-form" method="POST" action="">
                                    <input type="hidden" name="id_publicacion" value="<?php echo $row['id']; ?>">
                                    <input type="text" name="contenido_comentario" placeholder="Escribe un comentario">
                                    <button type="submit" name="comentar"><img src="assets/send.svg" alt=""></button>
                                </form>
                            </div>

                            <?php
// Mostrar los comentarios asociados a la publicación
$sql_comentarios = "SELECT comentarios.*, usuarios.nombre_usuario 
                    FROM comentarios 
                    INNER JOIN usuarios ON comentarios.id_usuario = usuarios.id 
                    WHERE comentarios.id_publicacion = ? 
                    ORDER BY comentarios.fecha_creacion DESC";
$stmt_comentarios = $conn->prepare($sql_comentarios);
$stmt_comentarios->bind_param("i", $row['id']);
$stmt_comentarios->execute();
$result_comentarios = $stmt_comentarios->get_result();

if ($result_comentarios->num_rows > 0) {
    while ($comentario = $result_comentarios->fetch_assoc()) {
        $claseComentario = (!empty($row['id_usuario']) && $_SESSION["id"] == $comentario['id_usuario']) ? 'comentario-propio' : 'comentario-otro';
        ?>
        <div class="pub-comentarios-hechos">
            <div class="pub-coment-info">
                 <img style="width:30px; height:30px" src="assets/user.png" alt="">
                <div>
                    <div class="pub-coment-info-text">
                        <h4><?php echo $comentario['nombre_usuario']; ?> </h4>
                                    
                         <p><?php echo $comentario['contenido']; ?></p>
                    </div>
                    <p> <?php echo $comentario['fecha_creacion']; ?></p>
                </div>
            </div>
            <img class="ventana-comen <?php echo $claseComentario; ?>" src="assets/menu.svg" alt="">
            <?php if((!empty($row['id_usuario']) && $_SESSION["id"] == $comentario['id_usuario']) && (($rol === 'admin'))){ ?>
            <div class="edit-delete-comm-container">
              <form method="POST" action="dashboard.php">
                  <input type="hidden" name="id_comentario" value="<?php echo $comentario['id']; ?>">
                  <input name="nuevo_contenido">
                  <button type="submit" name="editar_comentario">Editar Comentario</button>
                  <button type="submit" name="eliminar_comentario">Eliminar</button>
              </form>
              </div>
          <?php }elseif (!empty($row['id_usuario']) && $_SESSION["id"] == $comentario['id_usuario']){
              ?>
              <div class="edit-delete-comm-container">
                <form method="POST" action="dashboard.php">
                  <input type="hidden" name="id_comentario" value="<?php echo $comentario['id']; ?>">
                  <input name="nuevo_contenido">
                  <button type="submit" name="editar_comentario">Editar Comentario</button>
                  <button type="submit" name="eliminar_comentario">Eliminar</button>
              </form>
              </div>
          <?php } elseif($rol === 'admin'){
              ?>
                
              <form method="POST" action="dashboard.php">
                  <input type="hidden" name="id_comentario" value="<?php echo $comentario['id']; ?>">
                  <button type="submit" name="eliminar_comentario">Eliminar</button>
              </form>
          <?php } ?>

            
<div id="popup-comen" class="ventana-emergente popup-comen">
    <div class="contenido">
        <span class="cerrar" id="btnCerrar-comen">&times;</span>
                
    </div>
</div>



        </div>
<?php
    }
} else {
    echo "<p>No hay comentarios para esta publicación..</p>";
}
?>

                        </div>
                    
                        <div id="popup" class="ventana-emergente <?php echo ($rol === 'admin') ? 'publicacion-admin' : ((!empty($row['id_usuario']) && $_SESSION["id"] == $row['id_usuario_publicacion']) ? 'publicacion-user-container' : 'publicacion-otros-container'); ?>">
    <div class="contenido">
        <span class="cerrar" id="btnCerrar-publi">&times;</span>

        <?php
        // Verificar si el usuario es administrador
        if ($rol === 'admin') {
            echo '<div id="publicacion-admin">';
            echo '<form method="POST" action="">';
            echo '<input type="hidden" name="id_publicacion" value="' . $row['id'] . '">';
            echo '<button type="submit" name="eliminar_publicacion">Eliminar</button>';
            echo '</form>';
            echo '<h4>Información para administradores</h4>';
            echo '<div>Contenido visible solo para administradores</div>';
            echo '</div>';
        }

        // Verificar si el usuario es el dueño de la publicación
        elseif (!empty($row['id_usuario']) && $_SESSION["id"] == $row['id_usuario_publicacion']) {
            echo '<div id="publicacion-user">';
            echo '<form method="POST" action="">';
            echo '<input type="hidden" name="id_publicacion" value="' . $row['id'] . '">';
            echo '<input type="text" name="nuevo_contenido" placeholder="Nuevo contenido">';
            echo '<button type="submit" name="editar_publicacion">Editar</button>';
            echo '<button type="submit" name="eliminar_publicacion">Eliminar</button>';
            echo '</form>';
            echo '<h4>Información para el usuario dueño de la publicación</h4>';
            echo '<div>Contenido visible solo para el usuario dueño de la publicación</div>';
            
            // Formulario para editar o eliminar la publicación
          
            
            echo '</div>';
        }
        

        // Si el usuario no es administrador y no es el dueño de la publicación
        else {
            echo '<div id="publicacion-otros">';
            echo '<h4>Información para otros usuarios</h4>';
            echo '<div>Contenido visible para usuarios que no son administradores y no son dueños de la publicación</div>';
            echo '</div>';
        }
        ?>
    </div>
</div>

<?php  
                
                }
                } else {
                    echo '<h1 class="no-publicaciones">No hay publicaciones disponibles.</h1>';
                } 
                ?>
            
        </div>
        
        <div class="sec2">
            <div class="seccion-der">
                <h3>Usuarios Registrados</h3>
                <?php foreach ($usuarios as $usuario) { ?>
                    <div id="seccion-izq-user " class="seccion-izq-user sec-users-img" data-id="<?php echo obtenerIdUsuario($usuario); ?>" data-nombre="<?php echo $usuario; ?>" data-correo="<?php echo obtenerCorreoUsuario($usuario); ?>" data-rol="<?php echo obtenerRolUsuario($usuario); ?>">
                        <p><?php echo obtenerRolUsuario($usuario); ?></p>
                        <div class="users-img">
                            <img style="width:40px; height:40px" src="assets/user.png" alt="">
                            <h3><?php echo $usuario; ?></h3>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </section>


    <script>

function eliminarCuenta(idUsuario) {

    // Confirmar si el administrador realmente desea eliminar la cuenta del usuario
    if (confirm('¿Estás seguro de que deseas eliminar la cuenta de este usuario?')) {
        // Enviar el formulario para eliminar la cuenta
        document.getElementById('formEliminarUsuario').submit();
    }
}


    </script>

    <script>

// Obtener la ventana emergente y sus elementos
var ventanaEmergente = document.getElementById('ventanaEmergente');
var nombreUsuario = document.getElementById('nombreUsuario');
var correoUsuario = document.getElementById('correoUsuario');
var rolUsuario = document.getElementById('rolUsuario');
var btnCerrar = document.getElementById('btnCerrar');

// Función para mostrar la ventana emergente con la información del usuario
function mostrarInformacionUsuario(id, nombre, correo, rol) {
    // Mostrar información del usuario en la ventana emergente
    nombreUsuario.innerHTML = "Nombre: " + nombre;
    correoUsuario.innerHTML = "Correo: " + correo;
    rolUsuario.innerHTML = "Rol: " + rol;
    
    // Actualizar el valor del campo oculto con el ID del usuario
    document.getElementById('idUsuarioEliminar').value = id;
    
    // Mostrar la ventana emergente
    ventanaEmergente.style.display = 'block';
}

btnCerrar.addEventListener('click', function() {
    ventanaEmergente.style.display = 'none';
});

var userElements = document.querySelectorAll('.seccion-izq-user');

userElements.forEach(function(element) {
    element.addEventListener('click', function() {
        var id = element.getAttribute('data-id');
        var nombre = element.getAttribute('data-nombre');
        var correo = element.getAttribute('data-correo');
        var rol = element.getAttribute('data-rol');
        mostrarInformacionUsuario(id, nombre, correo, rol);
    });
});





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



      

//--------------------------------------------------------------------------------------

var btnMostrar1 = document.getElementById('btnMostrar1');
        var btnMostrar2 = document.getElementById('btnMostrar2');

        // Función para mostrar la ventana emergente
        function mostrarVentanaEmergente() {
            document.getElementById('ventanaEmergente2').style.display = 'block';
        }

        // Agrega el evento click a ambos botones para mostrar la ventana emergente
        btnMostrar1.addEventListener('click', mostrarVentanaEmergente);
        btnMostrar2.addEventListener('click', mostrarVentanaEmergente);

        // Agrega el evento click al botón de cerrar para ocultar la ventana emergente
        document.getElementById('btnCerrar-user').addEventListener('click', function() {
            document.getElementById('ventanaEmergente2').style.display = 'none';
        });

        //-----------------VENTANA EMERGENTE DE CONFIGURACION----------------------
        document.getElementById('btnMostrar-congf').addEventListener('click', function() {
            document.getElementById('ventanaEmergente-config').style.display = 'block';
        });

        document.getElementById('btnCerrar-config').addEventListener('click', function() {
            document.getElementById('ventanaEmergente-config').style.display = 'none';
        });




//------------------------ventana emergente de publicacion-----------------------

document.querySelectorAll('.ventana-publicacion').forEach(function(button) {
    button.addEventListener('click', function() {
        var esDueno = button.getAttribute('data-own');
        console.log("Abriendo popup");
        if (esDueno === 'true') {
            var popup = document.querySelector('.publicacion-user-container');
            if (popup) {
                popup.style.display = 'block';
            }
        } else if(esDueno === 'false') {
            var popup = document.querySelector('.publicacion-otros-container');
            if (popup) {
                popup.style.display = 'block';
            }
        }
        else if(esDueno === 'admin'){
            var popup = document.getElementById('popup');
            if (popup) {
                popup.style.display = 'block';
            }
        }
    });
});


document.querySelectorAll('.ventana-comen').forEach(function(button) {
  button.addEventListener('click', function() {
    // Obtiene el contenedor de edición y eliminación más cercano
    const closestEditDeleteCommContainer = button.nextElementSibling;

    if (closestEditDeleteCommContainer && closestEditDeleteCommContainer.classList.contains('edit-delete-comm-container')) {
      closestEditDeleteCommContainer.style.display = 'block';
    }
  });
});


document.querySelectorAll('.cerrar').forEach(function(button) {
    button.addEventListener('click', function() {
        button.closest('.ventana-emergente').style.display = 'none';
    });
});





        //-----------------------ventana de configuracion-------------------------

        // Obtener los elementos de cambio de opción y el formulario de eliminar cuenta
var cambioOpciones = document.querySelectorAll('.cambio-opcion');
var formEliminarCuenta = document.getElementById('form-eliminar-cuenta');

// Función para mostrar el campo correspondiente al hacer clic en una opción de cambio
cambioOpciones.forEach(function(opcion) {
    opcion.addEventListener('click', function(event) {
        event.preventDefault();
        var opcionSeleccionada = opcion.getAttribute('data-opcion');
        ocultarTodosLosCampos();
        mostrarCampo(opcionSeleccionada);
    });
});

// Función para ocultar todos los campos de cambio
function ocultarTodosLosCampos() {
    var camposCambio = document.querySelectorAll('[id^="cambio-"]');
    camposCambio.forEach(function(campo) {
        campo.style.display = 'none';
    });
}

// Función para mostrar un campo específico de cambio
function mostrarCampo(opcion) {
    var campoMostrar = document.getElementById('cambio-' + opcion);
    campoMostrar.style.display = 'block';
}

// Agregar evento submit al formulario de eliminar cuenta
formEliminarCuenta.addEventListener('submit', function(event) {
    var confirmarEliminar = confirm("¿Estás seguro de que deseas eliminar tu cuenta?");
    if (!confirmarEliminar) {
        event.preventDefault(); // Evitar que el formulario se envíe si el usuario cancela la acción
    }
});




// Función para enviar el formulario de actualización de usuario mediante AJAX
if(document.getElementById('btn-actualizar-usuario')){
document.getElementById('btn-actualizar-usuario').addEventListener('click', function() {
    var nuevoUsuario = document.getElementById('nuevo_usuario').value;

    // Crear objeto XMLHttpRequest
    var xhr = new XMLHttpRequest();

    // Configurar la solicitud AJAX
    xhr.open('POST', 'actualizar_usuario.php', true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

    // Definir la función de callback
    xhr.onload = function() {
        if (xhr.status >= 200 && xhr.status < 400) {
            // La solicitud fue exitosa
            var response = xhr.responseText;
            document.getElementById('message-container').innerHTML = response;
        } else {
            // Error en la solicitud
            console.error('Error en la solicitud AJAX');
        }
    };

    // Enviar la solicitud con los datos del formulario
    xhr.send('nuevo_usuario=' + nuevoUsuario);
});
}
    </script>
</body>
</html>