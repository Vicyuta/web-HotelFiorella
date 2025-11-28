<?php
include('../includes/header_admin.php');
include('../includes/db.php');

if (!isset($_GET['id'])) {
    header('Location: gestion_huespedes.php');
    exit();
}

$id = $_GET['id']; // PersonaID

// 1. Datos Personales
$stmt = $pdo->prepare("SELECT * FROM Persona WHERE PersonaID = ?");
$stmt->execute([$id]);
$persona = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$persona) {
    echo "<div class='container mt-5 alert alert-danger'>Huésped no encontrado.</div>";
    include('../includes/footer.php');
    exit();
}

// 2. Datos de Usuario (Si tiene cuenta)
$sql_user = "SELECT u.UsuarioID, u.NombreUsuario, u.RolID, u.Estado 
             FROM Usuario u 
             INNER JOIN Clientes c ON u.ClienteID = c.ClienteID 
             WHERE c.PersonaID = ?";
$stmt_user = $pdo->prepare($sql_user);
$stmt_user->execute([$id]);
$usuario_asociado = $stmt_user->fetch(PDO::FETCH_ASSOC);

// 3. Lista de Roles
$roles = $pdo->query("SELECT * FROM Rol")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid px-4">
    <div class="row align-items-center my-4">
        <div class="col">
            <h2 class="text-primary fw-bold"><i class="fas fa-user-edit me-2"></i>Editar Perfil de Huésped</h2>
            <p class="text-muted mb-0">Gestione la información personal y los permisos de acceso.</p>
        </div>
        <div class="col-auto">
            <a href="gestion_huespedes.php" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="fas fa-arrow-left me-2"></i>Volver
            </a>
        </div>
    </div>

    <form action="../actions/edit_huesped_process.php" method="POST">
        <input type="hidden" name="persona_id" value="<?php echo $persona['PersonaID']; ?>">
        
        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3 border-bottom-0">
                        <h5 class="m-0 text-dark fw-bold"><i class="far fa-id-card me-2 text-primary"></i>Información Personal</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small text-uppercase text-muted fw-bold">Nombres</label>
                                <input type="text" class="form-control form-control-lg" name="nombres" value="<?php echo htmlspecialchars($persona['Nombres']); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-uppercase text-muted fw-bold">Apellidos</label>
                                <input type="text" class="form-control form-control-lg" name="ape_paterno" value="<?php echo htmlspecialchars($persona['Ape_Paterno'] . ' ' . $persona['Ape_Materno']); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-uppercase text-muted fw-bold">Documento (DNI/Pasaporte)</label>
                                <input type="text" class="form-control" name="doc_identidad" value="<?php echo htmlspecialchars($persona['Doc_Identidad']); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-uppercase text-muted fw-bold">Correo Electrónico</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fas fa-envelope"></i></span>
                                    <input type="email" class="form-control" name="correo" value="<?php echo htmlspecialchars($persona['Correo']); ?>">
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label small text-uppercase text-muted fw-bold">Dirección de Domicilio</label>
                                <input type="text" class="form-control" name="direccion" value="<?php echo htmlspecialchars($persona['Direccion']); ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-lg border-0 bg-primary text-white mb-4 position-relative overflow-hidden">
                    <div style="position:absolute; top:-20px; right:-20px; font-size:100px; opacity:0.1;"><i class="fas fa-user-shield"></i></div>
                    
                    <div class="card-header border-white border-opacity-25 bg-transparent py-3">
                        <h5 class="m-0 fw-bold"><i class="fas fa-lock me-2"></i>Acceso al Sistema</h5>
                    </div>
                    
                    <div class="card-body">
                        <?php if ($usuario_asociado): ?>
                            <input type="hidden" name="usuario_id" value="<?php echo $usuario_asociado['UsuarioID']; ?>">
                            
                            <div class="mb-3">
                                <label class="form-label text-white-50">Usuario Asignado</label>
                                <input type="text" class="form-control bg-white bg-opacity-25 text-white border-0" value="<?php echo htmlspecialchars($usuario_asociado['NombreUsuario']); ?>" disabled>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold text-warning">Rol / Permisos</label>
                                <select class="form-select form-select-lg shadow-none text-dark" name="rol_id" style="border: 2px solid #ffc107;">
                                    <?php foreach ($roles as $rol): ?>
                                        <option value="<?php echo $rol['RolID']; ?>" <?php echo ($usuario_asociado['RolID'] == $rol['RolID']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($rol['NombreRol']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-text text-white-50 mt-2 small">
                                    <i class="fas fa-info-circle"></i> Cambie a "Administrador" para dar acceso total.
                                </div>
                            </div>

                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="fas fa-user-slash fa-3x mb-3 text-white-50"></i>
                                <p>Esta persona no tiene cuenta de usuario para acceder al sistema.</p>
                                <a href="add_usuario.php?persona_id=<?php echo $id; ?>" class="btn btn-light w-100 fw-bold text-primary">
                                    <i class="fas fa-plus-circle me-1"></i> Crear Usuario
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-success btn-lg shadow fw-bold">
                        <i class="fas fa-save me-2"></i> Guardar Cambios
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

