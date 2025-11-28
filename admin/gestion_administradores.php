<?php
include('../includes/header_admin.php');
include('../includes/db.php');

$registros_por_pagina = 10;
$pagina_actual = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($pagina_actual - 1) * $registros_por_pagina;

$params = [];
$filter_query_string = '';

// --- CONSULTA HÍBRIDA (Empleados + Clientes Ascendidos) ---
$base_sql = "
    FROM Usuario u
    LEFT JOIN Empleados e ON u.EmpleadoID = e.EmpleadoID
    LEFT JOIN Persona p_emp ON e.PersonaID = p_emp.PersonaID
    LEFT JOIN Clientes c ON u.ClienteID = c.ClienteID
    LEFT JOIN Persona p_cli ON c.PersonaID = p_cli.PersonaID
    WHERE u.RolID = 1 AND u.Estado = '1'
";

if (!empty($_GET['q'])) {
    $q_filtro = '%' . $_GET['q'] . '%';
    $base_sql .= " AND (
        COALESCE(p_emp.Nombres, p_cli.Nombres) LIKE ? OR 
        COALESCE(p_emp.Ape_Paterno, p_cli.Ape_Paterno) LIKE ? OR 
        u.NombreUsuario LIKE ?
    )";
    array_push($params, $q_filtro, $q_filtro, $q_filtro);
    $filter_query_string .= '&q=' . urlencode($_GET['q']);
}

// Totales
$total_sql = "SELECT COUNT(*) " . $base_sql;
$stmt_total = $pdo->prepare($total_sql);
$stmt_total->execute($params);
$total_registros = $stmt_total->fetchColumn();
$total_paginas = ceil($total_registros / $registros_por_pagina);

// Datos
$sql = "SELECT 
            u.UsuarioID, 
            u.NombreUsuario AS Email, 
            COALESCE(p_emp.Nombres, p_cli.Nombres) as Nombres, 
            COALESCE(p_emp.Ape_Paterno, p_cli.Ape_Paterno) as Ape_Paterno, 
            COALESCE(p_emp.Ape_Materno, p_cli.Ape_Materno) as Ape_Materno,
            CASE WHEN u.EmpleadoID IS NOT NULL THEN 'Empleado' ELSE 'Cliente Ascendido' END as Origen
        " . $base_sql . "
        ORDER BY COALESCE(p_emp.Ape_Paterno, p_cli.Ape_Paterno)
        OFFSET ? ROWS FETCH NEXT ? ROWS ONLY";

$stmt = $pdo->prepare($sql);
$params_paginacion = array_merge($params, [$offset, $registros_por_pagina]);
$param_index = 1;
foreach ($params_paginacion as &$param_value) {
    $param_type = is_int($param_value) ? PDO::PARAM_INT : PDO::PARAM_STR;
    $stmt->bindValue($param_index, $param_value, $param_type);
    $param_index++;
}
$stmt->execute();
$admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center my-4">
        <h2 class="text-primary fw-bold"><i class="fas fa-user-tie me-2"></i>Gestión de Administradores</h2>
        <a href="add_administrador.php" class="btn btn-success shadow fw-bold">
            <i class="fas fa-plus-circle me-2"></i> Nuevo Admin
        </a>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-3 bg-light rounded">
            <form action="gestion_administradores.php" method="GET" class="d-flex gap-2">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-primary"><i class="fas fa-search"></i></span>
                    <input type="text" name="q" class="form-control border-start-0" placeholder="Buscar por nombre o usuario..." value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>">
                </div>
                <button type="submit" class="btn btn-primary px-4 fw-bold">Filtrar</button>
                <?php if(!empty($_GET['q'])): ?>
                    <a href="gestion_administradores.php" class="btn btn-outline-secondary">Limpiar</a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <div class="card shadow border-0 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th class="ps-4 py-3">Nombre Completo</th>
                            <th class="py-3">Usuario (Login)</th>
                            <th class="py-3 text-center">Tipo</th>
                            <th class="text-end pe-4 py-3">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($admins) > 0): ?>
                            <?php foreach ($admins as $admin): ?>
                            <tr>
                                <td class="ps-4 fw-bold text-dark">
                                    <?php echo htmlspecialchars($admin['Nombres'] . ' ' . $admin['Ape_Paterno'] . ' ' . $admin['Ape_Materno']); ?>
                                </td>
                                <td class="text-muted">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-light rounded-circle p-2 me-2 text-primary">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <?php echo htmlspecialchars($admin['Email']); ?>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <?php if($admin['Origen'] == 'Empleado'): ?>
                                        <span class="badge bg-info text-dark shadow-sm"><i class="fas fa-building me-1"></i> Interno</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark shadow-sm"><i class="fas fa-user-tag me-1"></i> Externo</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="edit_usuario.php?id=<?php echo $admin['UsuarioID']; ?>" class="btn btn-sm btn-primary shadow-sm text-white" title="Editar Credenciales">
                                        <i class="fas fa-edit me-1"></i> Editar
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <div class="mb-3">
                                        <i class="fas fa-users-slash fa-4x text-light"></i>
                                    </div>
                                    <h5 class="fw-bold">No se encontraron administradores</h5>
                                    <p class="small mb-0">Intente ajustar los filtros de búsqueda.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php if ($total_paginas > 1): ?>
    <div class="d-flex justify-content-center mt-4 mb-5">
        <nav aria-label="Page navigation">
            <ul class="pagination shadow-sm">
                <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                    <li class="page-item <?php echo ($i == $pagina_actual) ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i . $filter_query_string; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>
    <?php endif; ?>
</div>
