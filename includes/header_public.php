<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Hotel Fiorella</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="stylesheet" href="assets/css/style.css?v=2">
    
</head>
<body>
    <header class="main-header">
        <div class="container">
            <div class="logo">
                <a href="index.php">
                    <img src="img/logo.png" alt="Hotel Fiorella" style="max-height: 60px;">
                </a>
            </div>
            
            <nav class="main-nav">
                <ul class="menu">
                    <li><a href="index.php">Inicio</a></li>
                    <li><a href="habitaciones.php">Habitaciones</a></li>
                    <li><a href="reservar.php">Reservar</a></li>
                </ul>
            </nav>

            <button class="menu-toggle" aria-label="Toggle navigation">
                <i class="fas fa-bars"></i>
            </button>
            
            <div class="auth-buttons">
                <?php if (isset($_SESSION['usuario_id'])): ?>
                    <?php if ($_SESSION['rol_id'] == 1): ?>
                         <a href="admin/" class="btn btn-primary">Panel Admin</a>
                    <?php else: ?>
                         <a href="client/" class="btn btn-primary">Mi Cuenta</a>
                    <?php endif; ?>
                    <a href="actions/logout.php" class="btn btn-secondary">Salir</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-primary">Ingresar</a>
                    <a href="register.php" class="btn btn-secondary">Registrarse</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const menuToggle = document.querySelector('.menu-toggle');
            const mainNav = document.querySelector('.main-nav');

            if (menuToggle && mainNav) {
                menuToggle.addEventListener('click', function() {
                    mainNav.classList.toggle('active');
                });
            }
        });
    </script>