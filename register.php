<?php include('includes/header_public.php'); ?>

<style>
    .card-registro { border: none; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
    .card-header-registro { background: #0d6efd; color: white; border-radius: 15px 15px 0 0 !important; padding: 1.5rem; text-align: center; }
    .selector-group .btn-check:checked + .btn-outline-primary { background-color: #0d6efd; color: white; border-color: #0d6efd; box-shadow: 0 4px 8px rgba(13, 110, 253, 0.2); }
    .selector-group .btn-outline-primary { color: #555; border-color: #ddd; background-color: #f8f9fa; }
    .selector-group .btn-outline-primary:hover { background-color: #e9ecef; border-color: #ccc; color: #0d6efd; }
    .form-section-heading { font-size: 0.9rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #adb5bd; margin-top: 1.5rem; margin-bottom: 1rem; border-bottom: 1px solid #eee; padding-bottom: 5px; }
</style>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-7">
            <div class="card card-registro">
                <div class="card-header card-header-registro">
                    <h3 class="mb-1 fw-bold"><i class="fas fa-user-plus me-2"></i>Crear Cuenta</h3>
                    <p class="mb-0 opacity-75">Complete el formulario para registrarse</p>
                </div>
                <div class="card-body p-4 p-md-5">
                    
                    <div class="mb-4">
                        <label class="form-label text-muted small fw-bold mb-2 d-block text-center">SELECCIONE TIPO DE CUENTA:</label>
                        <div class="btn-group w-100 selector-group" role="group">
                            <input type="radio" class="btn-check" name="btnradio" id="btnPersona" autocomplete="off" checked onclick="toggleForm('persona')">
                            <label class="btn btn-outline-primary py-3" for="btnPersona">
                                <i class="fas fa-user me-2 fs-5"></i><br>Persona Natural
                            </label>

                            <input type="radio" class="btn-check" name="btnradio" id="btnEmpresa" autocomplete="off" onclick="toggleForm('empresa')">
                            <label class="btn btn-outline-primary py-3" for="btnEmpresa">
                                <i class="fas fa-building me-2 fs-5"></i><br>Empresa (RUC)
                            </label>
                        </div>
                    </div>

                    <form action="actions/register_process.php" method="POST">
                        <input type="hidden" name="tipo_registro" id="tipo_registro" value="persona">

                        <div id="form-persona">
                            <div class="form-section-heading">Datos Personales</div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" name="nombres" id="nombres" placeholder="Nombres">
                                        <label>Nombres *</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" name="ape_paterno" id="ape_paterno" placeholder="Apellidos">
                                        <label>Apellido Paterno *</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" name="ape_materno" id="ape_materno" placeholder="Apellidos">
                                        <label>Apellido Materno</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" name="doc_identidad" id="doc_identidad" placeholder="DNI">
                                        <label>DNI / Documento *</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <?php $fecha_maxima = date('Y-m-d', strtotime('-18 years')); ?>
                                        <input type="date" class="form-control" name="fec_nacimiento" id="fec_nacimiento" max="<?php echo $fecha_maxima; ?>">
                                        <label>Fecha de Nacimiento *</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select class="form-select" name="estado_civil" id="estado_civil">
                                            <option value="Soltero/a">Soltero/a</option>
                                            <option value="Casado/a">Casado/a</option>
                                            <option value="Divorciado/a">Divorciado/a</option>
                                            <option value="Viudo/a">Viudo/a</option>
                                        </select>
                                        <label>Estado Civil *</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select class="form-select" name="genero">
                                            <option value="M">Masculino</option>
                                            <option value="F">Femenino</option>
                                        </select>
                                        <label>Género</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" name="celular" placeholder="Celular">
                                        <label>Celular</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" name="direccion_persona" placeholder="Dirección">
                                        <label>Dirección Domiciliaria</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-floating">
                                        <input type="email" class="form-control" name="correo" id="correo" placeholder="Email">
                                        <label>Correo Electrónico (Será su Usuario) *</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="form-empresa" style="display: none;">
                            <div class="form-section-heading">Datos Corporativos</div>
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" name="ruc" id="ruc" placeholder="RUC" maxlength="11">
                                        <label>R.U.C. (11 dígitos) *</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" name="razon_social" id="razon_social" placeholder="Razón Social">
                                        <label>Razón Social *</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" name="direccion_empresa" id="direccion_empresa" placeholder="Dirección Fiscal">
                                        <label>Dirección Fiscal *</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" name="telefono_empresa" placeholder="Teléfono" maxlength="9">
                                        <label>Teléfono de Contacto</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-floating">
                                        <input type="email" class="form-control" name="correo_empresa" id="correo_empresa" placeholder="Email Empresa">
                                        <label>Correo de la Empresa (Usuario) *</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-section-heading mt-4">Seguridad</div>
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="alert alert-info py-2 small">
                                    <i class="fas fa-info-circle me-1"></i> Su nombre de usuario será su <strong>Correo Electrónico</strong>.
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating">
                                    <input type="password" class="form-control" name="contrasena" placeholder="Contraseña" required>
                                    <label>Contraseña *</label>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid mt-5">
                            <button type="submit" class="btn btn-primary btn-lg fw-bold py-3 shadow-sm">
                                Registrarme Ahora
                            </button>
                        </div>
                        
                        <div class="text-center mt-4">
                            <p class="text-muted">¿Ya tienes cuenta? <a href="login.php" class="fw-bold text-decoration-none">Inicia Sesión</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleForm(tipo) {
    document.getElementById('tipo_registro').value = tipo;
    
    const formPersona = document.getElementById('form-persona');
    const formEmpresa = document.getElementById('form-empresa');
    
    const reqPersona = ['nombres', 'ape_paterno', 'doc_identidad', 'fec_nacimiento', 'estado_civil', 'correo'];
    const reqEmpresa = ['ruc', 'razon_social', 'direccion_empresa', 'correo_empresa'];

    if (tipo === 'persona') {
        formPersona.style.display = 'block';
        formEmpresa.style.display = 'none';
        
        reqPersona.forEach(id => document.getElementById(id).required = true);
        reqEmpresa.forEach(id => document.getElementById(id).required = false);
    } else {
        formPersona.style.display = 'none';
        formEmpresa.style.display = 'block';
        
        reqPersona.forEach(id => document.getElementById(id).required = false);
        reqEmpresa.forEach(id => document.getElementById(id).required = true);
    }
}
toggleForm('persona');
</script>
