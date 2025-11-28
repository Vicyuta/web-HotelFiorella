<?php
include('../includes/header_admin.php');
include('../includes/db.php');

if (!isset($_GET['id'])) {
    header('Location: gestion_usuarios.php');
    exit();
}

$id = $_GET['id'];

// 1. Obtener datos del Usuario
$stmt = $pdo->prepare("SELECT * FROM Usuario WHERE UsuarioID = ?");
$stmt->execute([$id]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    echo "<div class='container mt-5'><div class='alert alert-danger'>Usuario no encontrado.</div></div>";
    include('../includes/footer.php');
    exit();
}

// 2. Obtener lista de Roles
$stmt_roles = $pdo->query("SELECT * FROM Rol");
$roles = $stmt_roles->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid px-4">
    <div class="row align-items-center my-4">
        <div class="col">
            <h2 class="text-primary fw-bold"><i class="fas fa-user-shield me-2"></i>Editar Usuario</h2>
            <p class="text-muted mb-0">Gestione las credenciales y niveles de acceso del sistema.</p>
        </div>
        <div class="col-auto">
            <a href="gestion_usuarios.php" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="fas fa-arrow-left me-2"></i>Volver
            </a>
        </div>
    </div>

    <form action="../actions/edit_usuario_process.php" method="POST">
        <input type="hidden" name="usuario_id" value="<?php echo $usuario['UsuarioID']; ?>">

        <div class="row">
            <div class="col-lg-7">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3 border-bottom-0">
                        <h5 class="m-0 text-dark fw-bold"><i class="fas fa-key me-2 text-primary"></i>Credenciales de Acceso</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <label class="form-label small text-uppercase text-muted fw-bold">Nombre de Usuario</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-light border-end-0"><i class="fas fa-user text-secondary"></i></span>
                                <input type="text" class="form-control border-start-0 ps-0" name="nombre_usuario" value="<?php echo htmlspecialchars($usuario['NombreUsuario']); ?>" required>
                            </div>
                            <div class="form-text">Este es el nombre único para iniciar sesión.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small text-uppercase text-muted fw-bold">Nueva Contraseña</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-light border-end-0"><i class="fas fa-lock text-secondary"></i></span>
                                <input type="password" class="form-control border-start-0 ps-0" name="contrasena" placeholder="••••••••">
                            </div>
                            <div class="form-text text-info"><i class="fas fa-info-circle"></i> Deje este campo vacío si <strong>NO</strong> desea cambiar la contraseña actual.</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card shadow-lg border-0 bg-white mb-4">
                    <div class="card-header bg-primary text-white py-3">
                        <h5 class="m-0 fw-bold"><i class="fas fa-shield-alt me-2"></i>Control de Acceso</h5>
                    </div>
                    <div class="card-body bg-light">
                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark">Rol / Nivel de Permiso</label>
                            <select class="form-select form-select-lg border-primary shadow-sm" name="rol_id" required>
                                <?php foreach ($roles as $rol): ?>
                                    <option value="<?php echo $rol['RolID']; ?>" <?php echo ($usuario['RolID'] == $rol['RolID']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($rol['NombreRol']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="mt-2 small text-muted">
                                <span class="badge bg-warning text-dark me-1">Nota:</span>
                                El rol de "Administrador" otorga control total.
                            </div>
                        </div>

                        <hr class="text-secondary opacity-25">

                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark">Estado de la Cuenta</label>
                            <div class="d-flex gap-2">
                                <div class="form-check custom-option w-50">
                                    <input class="form-check-input" type="radio" name="estado" id="estadoActive" value="1" <?php echo ($usuario['Estado'] == '1') ? 'checked' : ''; ?>>
                                    <label class="form-check-label w-100 p-2 border rounded text-center bg-white" for="estadoActive">
                                        <i class="fas fa-check-circle text-success d-block mb-1 fs-4"></i>
                                        Activo
                                    </label>
                                </div>
                                <div class="form-check custom-option w-50">
                                    <input class="form-check-input" type="radio" name="estado" id="estadoInactive" value="0" <?php echo ($usuario['Estado'] == '0') ? 'checked' : ''; ?>>
                                    <label class="form-check-label w-100 p-2 border rounded text-center bg-white" for="estadoInactive">
                                        <i class="fas fa-ban text-danger d-block mb-1 fs-4"></i>
                                        Inactivo
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg shadow fw-bold">
                        <i class="fas fa-save me-2"></i> Guardar Cambios
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
    .custom-option .form-check-input { position: absolute; clip: rect(0,0,0,0); pointer-events: none; }
    .custom-option .form-check-label { cursor: pointer; transition: all 0.2s; border: 2px solid transparent !important; }
    .custom-option .form-check-input:checked + .form-check-label { border-color: #0d6efd !important; background-color: #e7f1ff !important; color: #0d6efd; font-weight: bold; }
    .input-group-text { background-color: #fff; }
</style>
