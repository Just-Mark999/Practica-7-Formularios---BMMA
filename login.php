<?php
session_start();
$conexion = new mysqli("localhost", "root", "", "tienda_celulares");

$correo = $_POST['correo'];
$contrasena_ingresada = $_POST['contrasena'];

$stmt = $conexion->prepare("SELECT id, contrasena, nombre FROM usuarios WHERE correo = ?");
$stmt->bind_param("s", $correo);
$stmt->execute();
$resultado = $stmt->get_result();

if ($fila = $resultado->fetch_assoc()) {
    if (password_verify($contrasena_ingresada, $fila['contrasena'])) {
        $_SESSION['usuario'] = $fila['nombre'];
        header("Location: contacto.html?login=exito&nombre=" . urlencode($fila['nombre']));
        exit();
    } else {
        echo "Contraseña incorrecta.";
    }
} else {
    echo "El correo no está registrado.";
}
?>