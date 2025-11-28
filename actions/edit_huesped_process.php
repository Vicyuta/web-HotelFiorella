<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include('../includes/db.php');

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_id'] != 1 || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../login.php');
    exit();
}

$persona_id = $_POST['persona_id'];
$nombres = $_POST['nombres'];
$ape_paterno = $_POST['ape_paterno'];
// (Nota: Si tu base de datos tiene Ape_Materno, recíbelo aquí también)
$doc_identidad = $_POST['doc_identidad'];
$correo = $_POST['correo'];
$direccion = $_POST['direccion'];

$usuario_id = $_POST['usuario_id'] ?? null;
$rol_id = $_POST['rol_id'] ?? null;

try {
    $pdo->beginTransaction();

    // 1. Actualizar Persona
    $sql_persona = "UPDATE Persona SET Nombres = ?, Ape_Paterno = ?, Doc_Identidad = ?, Correo = ?, Direccion = ? WHERE PersonaID = ?";
    $stmt = $pdo->prepare($sql_persona);
    $stmt->execute([$nombres, $ape_paterno, $doc_identidad, $correo, $direccion, $persona_id]);

    // 2. Actualizar Rol de Usuario (Solo si existe usuario_id)
    if ($usuario_id && $rol_id) {
        $sql_usuario = "UPDATE Usuario SET RolID = ? WHERE UsuarioID = ?";
        $stmt_u = $pdo->prepare($sql_usuario);
        $stmt_u->execute([$rol_id, $usuario_id]);
    }

    $pdo->commit();
    header('Location: ../admin/gestion_huespedes.php?success=edit_ok');
    exit();

} catch (PDOException $e) {
    if ($pdo->inTransaction()) { $pdo->rollBack(); }
    header('Location: ../admin/edit_huesped.php?id=' . $persona_id . '&error=' . urlencode($e->getMessage()));
    exit();
}
?>