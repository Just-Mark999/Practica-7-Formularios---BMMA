<?php
$datos_recibidos = file_get_contents("php://input");
$pedido = json_decode($datos_recibidos, true);

if (!$pedido || empty($pedido['carrito'])) {
    die("Error: Datos de carrito vacíos.");
}

$conexion = new mysqli("localhost", "root", "", "tienda_celulares");
if ($conexion->connect_error) {
    die("Fallo en la conexión: " . $conexion->connect_error);
}

// Variables capturadas
$metodo = $pedido['metodo'];
$total = $pedido['total'];
$nombre = $pedido['nombre'];
$correo = $pedido['correo'];
$telefono = $pedido['telefono'];
$direccion = $pedido['direccion'];
$carrito = $pedido['carrito'];

// 1. Insertar la venta general con los datos del usuario
$stmt_venta = $conexion->prepare("INSERT INTO ventas (nombre_completo, correo, telefono, direccion, metodo_pago, total) VALUES (?, ?, ?, ?, ?, ?)");
// "sssssd" = 5 strings (nombre, correo, telefono, direccion, metodo) y 1 double (total)
$stmt_venta->bind_param("sssssd", $nombre, $correo, $telefono, $direccion, $metodo, $total);

if ($stmt_venta->execute()) {
    $venta_id = $conexion->insert_id;
    
    // 2. Insertar los detalles
    $stmt_detalle = $conexion->prepare("INSERT INTO detalles_venta (venta_id, nombre_producto, cantidad, precio) VALUES (?, ?, ?, ?)");
    
    foreach ($carrito as $item) {
        $nombre_prod = $item['nombre'];
        $cantidad = $item['cantidad'];
        $precio = $item['precio'];
        
        $stmt_detalle->bind_param("isid", $venta_id, $nombre_prod, $cantidad, $precio);
        $stmt_detalle->execute();
    }
    
    $stmt_detalle->close();
    echo "Compra registrada correctamente";
} else {
    echo "Error al registrar la venta: " . $conexion->error;
}

$stmt_venta->close();
$conexion->close();
?>