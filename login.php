<?php
include 'config.php'; // Incluye el archivo de configuración para la conexión a la BD
session_start();

$message = ''; // Para guardar mensajes de error

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST['email']) && isset($_POST['password'])) {
        $correo = $_POST['email'];
        $password = $_POST['password'];

        // Modificar la consulta para seleccionar el nombre del usuario
$stmt = $conn->prepare("SELECT id_usuario, nombre, rol FROM Usuarios WHERE correo = :correo AND password = :password");
$stmt->bindParam(':correo', $correo, PDO::PARAM_STR);
$stmt->bindParam(':password', $password, PDO::PARAM_STR);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    // Guardar el nombre del usuario en la sesión
    $_SESSION['user_id'] = $user['id_usuario'];
    $_SESSION['nombre_usuario'] = $user['nombre']; // Guardar el nombre del usuario
    $rol = $user['rol'];

    // Redirigir según el rol
    switch ($rol) {
        case 'admin':
            header('Location: admin_panel.php');
            break;
        case 'asesor':
            header('Location: asesor_panel.php');
            break;
        case 'bodeguero':
            header('Location: bodeguero_panel.php');
            break;
        default:
            $message = 'Rol desconocido. Contacte al administrador.';
    }
        } else {
            $message = '<input type="checkbox" id="close-alert" style="display: none;">
                        <div class="alert">
                            Credenciales incorrectas.
                            <br>
                            <a href="index.html" class="close-alert">Cerrar</a>
                        </div>';
        }
    } else {
        $message = 'Por favor, complete todos los campos.';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .alert {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            padding: 70px;
            font-size: 40px;
            background-color: #e91d29;
            color: white;
            border-radius: 8px;
            z-index: 1000;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
        }
        .close-alert {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 10px;
            background-color: white;
            color: #e91d29;
            border: none;
            padding: 20px 40px;
            cursor: pointer;
            border-radius: 4px;
            transition: background-color 0.3s;
            text-decoration: none;
        }
        .close-alert:hover {
            background-color: #f5f5f5;
        }
        #close-alert:checked + .alert {
            display: none;
        }
    </style>
</head>
<body>
    <div class="container2">
        <?php echo $message; ?>
    </div>
</body>
</html>
