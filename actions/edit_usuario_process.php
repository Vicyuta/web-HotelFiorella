<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include('../includes/db.php');

// Seguridad: Solo admin
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_id'] != 1 || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../login.php');
    exit();
}

$usuario_id = $_POST['usuario_id'];
$nombre_usuario = $_POST['nombre_usuario'];
$rol_id = $_POST['rol_id'];
$estado = $_POST['estado'];
$contrasena = $_POST['contrasena'];

try {
    // Verificar duplicados
    $stmt_check = $pdo->prepare("SELECT UsuarioID FROM Usuario WHERE NombreUsuario = ? AND UsuarioID != ?");
    $stmt_check->execute([$nombre_usuario, $usuario_id]);
    if ($stmt_check->fetch()) {
        header('Location: ../admin/edit_usuario.php?id=' . $usuario_id . '&error=usuario_duplicado');
        exit();
    }

    // Actualizar datos
    if (!empty($contrasena)) {
        // Con contraseña nueva
        $pass_final = $contrasena; // O usa password_hash si ya lo implementaste
        $sql = "UPDATE Usuario SET NombreUsuario = ?, RolID = ?, Estado = ?, Contrasena = ? WHERE UsuarioID = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nombre_usuario, $rol_id, $estado, $pass_final, $usuario_id]);
    } else {
        // Sin cambiar contraseña
        $sql = "UPDATE Usuario SET NombreUsuario = ?, RolID = ?, Estado = ? WHERE UsuarioID = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nombre_usuario, $rol_id, $estado, $usuario_id]);
    }

    // --- CORRECCIÓN AQUÍ ---
    // Antes redirigía a 'gestion_usuarios.php' (que no existe).
    // Ahora redirige a 'gestion_administradores.php' para que veas el cambio.
    header('Location: ../admin/gestion_administradores.php?success=edit_ok');
    exit();

} catch (PDOException $e) {
    header('Location: ../admin/edit_usuario.php?id=' . $usuario_id . '&error=' . urlencode($e->getMessage()));
    exit();
}
?>