<?php
// Configuración de la conexión
$servidor = "localhost";
$usuario_db = "root";
$password_db = "";
$nombre_bd = "tienda_celulares";

$conexion = new mysqli($servidor, $usuario_db, $password_db, $nombre_bd);

if ($conexion->connect_error) {
    die("Fallo en la conexión: " . $conexion->connect_error);
}

// Captura de datos usando los nombres (name) de los inputs del HTML
$nombre = $_POST['nombre'];
$correo = $_POST['correo'];
$telefono = $_POST['telefono'];
// Es indispensable encriptar la contraseña antes de guardarla en SQL
$contrasena = password_hash($_POST['contrasena'], PASSWORD_BCRYPT); 

// Sentencia SQL preparada para evitar inyecciones
$stmt = $conexion->prepare("INSERT INTO usuarios (nombre, correo, telefono, contrasena) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $nombre, $correo, $telefono, $contrasena);

if ($stmt->execute()) {
    // Redirige de vuelta a la página de contacto
    header("Location: contacto.html?registro=exito");
    exit(); // Es una buena práctica detener el script después de una redirección
} else {
    echo "Error al registrar la cuenta: " . $conexion->error;
}

$stmt->close();
$conexion->close();
?>