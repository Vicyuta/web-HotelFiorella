<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include('../includes/db.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../register.php');
    exit();
}

$tipo_registro = $_POST['tipo_registro'];
$contrasena = $_POST['contrasena'];
$usuario_login = ''; 

try {
    $pdo->beginTransaction();

    $cliente_id = null;

    if ($tipo_registro == 'persona') {
        // --- LOGICA PERSONA ---
        $nombres = $_POST['nombres'];
        $ape_paterno = $_POST['ape_paterno'];
        $ape_materno = $_POST['ape_materno'];
        $dni = $_POST['doc_identidad'];
        $fecha_nac = $_POST['fec_nacimiento'];
        $genero = $_POST['genero'];
        $civil = $_POST['estado_civil'];
        $celular = $_POST['celular'];
        $direccion = $_POST['direccion_persona'];
        $correo = $_POST['correo'];
        
        $usuario_login = $correo;

        // 1. Validar Usuario (Correo)
        $stmt_check = $pdo->prepare("SELECT UsuarioID FROM Usuario WHERE NombreUsuario = ?");
        $stmt_check->execute([$usuario_login]);
        if ($stmt_check->fetch()) { throw new Exception("El correo electrónico ya está registrado como usuario."); }

        // 2. Validar DNI duplicado
        $stmt_dni = $pdo->prepare("SELECT PersonaID FROM Persona WHERE Doc_Identidad = ?");
        $stmt_dni->execute([$dni]);
        if ($stmt_dni->fetch()) { throw new Exception("El número de Documento/DNI ya está registrado."); }

        // 3. Validar Celular duplicado
        if (!empty($celular)) {
            $stmt_cel = $pdo->prepare("SELECT PersonaID FROM Persona WHERE Celular = ?");
            $stmt_cel->execute([$celular]);
            if ($stmt_cel->fetch()) { throw new Exception("El número de celular ya está registrado."); }
        }

        // Insertar Persona
        $sql_per = "INSERT INTO Persona (DistritoID, TipoDocumentoID, Nombres, Ape_Paterno, Ape_Materno, Fec_Nacimiento, Doc_Identidad, Direccion, Celular, E_Civil, Genero, Correo, Estado) 
                    VALUES (1, 1, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, '1')";
        $stmt_per = $pdo->prepare($sql_per);
        $stmt_per->execute([$nombres, $ape_paterno, $ape_materno, $fecha_nac, $dni, $direccion, $celular, $civil, $genero, $correo]);
        $persona_id = $pdo->lastInsertId();

        // Crear Cliente
        $sql_cli = "INSERT INTO Clientes (PersonaID, EmpresaID, Estado) VALUES (?, NULL, '1')";
        $stmt_cli = $pdo->prepare($sql_cli);
        $stmt_cli->execute([$persona_id]);
        $cliente_id = $pdo->lastInsertId();

    } else {
        // --- LOGICA EMPRESA ---
        $ruc = $_POST['ruc'];
        $razon_social = $_POST['razon_social'];
        $direccion_emp = $_POST['direccion_empresa'];
        $telefono_emp = $_POST['telefono_empresa'];
        $correo_empresa = $_POST['correo_empresa']; 
        
        $usuario_login = $correo_empresa;

        // 1. Validar Usuario (Correo Empresa)
        $stmt_check = $pdo->prepare("SELECT UsuarioID FROM Usuario WHERE NombreUsuario = ?");
        $stmt_check->execute([$usuario_login]);
        if ($stmt_check->fetch()) { throw new Exception("El correo de la empresa ya está registrado."); }

        if (strlen($ruc) !== 11) { throw new Exception("El RUC debe tener 11 dígitos."); }

        // 2. Validar RUC duplicado
        $stmt_ruc = $pdo->prepare("SELECT EmpresaID FROM Empresa WHERE RUC = ?");
        $stmt_ruc->execute([$ruc]);
        if ($stmt_ruc->fetch()) { throw new Exception("El número de RUC ya se encuentra registrado."); }

        // 3. Validar Teléfono duplicado (ESTE ERA TU ERROR)
        $stmt_tel = $pdo->prepare("SELECT EmpresaID FROM Empresa WHERE Telefono = ?");
        $stmt_tel->execute([$telefono_emp]);
        if ($stmt_tel->fetch()) { throw new Exception("El teléfono ya está registrado por otra empresa."); }

        // 4. Validar Razón Social duplicada
        $stmt_rs = $pdo->prepare("SELECT EmpresaID FROM Empresa WHERE Razon_Social = ?");
        $stmt_rs->execute([$razon_social]);
        if ($stmt_rs->fetch()) { throw new Exception("La Razón Social ya está registrada."); }

        // Insertar Empresa
        $sql_emp = "INSERT INTO Empresa (RUC, Razon_Social, Direccion, Telefono, Estado) VALUES (?, ?, ?, ?, '1')";
        $stmt_emp = $pdo->prepare($sql_emp);
        $stmt_emp->execute([$ruc, $razon_social, $direccion_emp, $telefono_emp]);
        $empresa_id = $pdo->lastInsertId();

        // Crear Cliente
        $sql_cli = "INSERT INTO Clientes (PersonaID, EmpresaID, Estado) VALUES (NULL, ?, '1')";
        $stmt_cli = $pdo->prepare($sql_cli);
        $stmt_cli->execute([$empresa_id]);
        $cliente_id = $pdo->lastInsertId();
    }

    // Crear Usuario
    $sql_user = "INSERT INTO Usuario (EmpleadoID, ClienteID, RolID, NombreUsuario, Contrasena, Estado) 
                 VALUES (NULL, ?, 2, ?, ?, '1')";
    $stmt_user = $pdo->prepare($sql_user);
    $stmt_user->execute([$cliente_id, $usuario_login, $contrasena]);

    $pdo->commit();
    
    header('Location: ../login.php?success=registro_ok');
    exit();

} catch (Exception $e) {
    if ($pdo->inTransaction()) { $pdo->rollBack(); }
    
    // Capturamos cualquier otro error inesperado
    echo "<script>
        alert('" . addslashes($e->getMessage()) . "'); 
        window.history.back();
    </script>";
    exit();
}
?>