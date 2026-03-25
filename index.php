<?php
session_start();
// Si ya está logueado, mandarlo al dashboard directamente
if (isset($_SESSION['user_acc_id'])) {
    header("Location: modules/dashboard/dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biblioteca Digital | Plataforma de Gestión</title>
    <!-- CSS -->
    <link rel="stylesheet" href="assets/global.css">
    <link rel="stylesheet" href="assets/home.css">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="home-page">
    <div class="home-overlay"></div>
    
    <nav class="navbar">
        <div class="brand">
            <i class="fa fa-book-reader" style="color:var(--primary);"></i>
            <span>SIS-BIBLIO</span>
        </div>
        <div class="auth-buttons">
            <a href="login.php" class="btn-login"><i class="fa fa-sign-in-alt"></i> Iniciar Sesión</a>
            <a href="register.php" class="btn-register"><i class="fa fa-user-plus"></i> Crear Cuenta</a>
        </div>
    </nav>

    <div class="hero">
        <h1>Gestión Integral de Biblioteca</h1>
        <p>Sistema administrativo y módulo de seguridad web orientado al control de usuarios, catálogo literario y préstamos.</p>
        

    </div>

    <div class="features" id="features">
        <div class="feature-card">
            <i class="fa fa-users-cog"></i>
            <h3>Control de Usuarios</h3>
            <p>Administración de cuentas con separación de niveles de acceso entre el Staff del sistema y los Lectores.</p>
        </div>
        <div class="feature-card">
            <i class="fa fa-book-reader"></i>
            <h3>Catálogo y Préstamos</h3>
            <p>Seguimiento en tiempo real de la disponibilidad bibliográfica y registro histórico de libros prestados.</p>
        </div>
        <div class="feature-card">
            <i class="fa fa-shield-alt"></i>
            <h3>Módulo de Seguridad</h3>
            <p>Protección integrada contra XSS, Inyección SQL, bloqueos por fuerza bruta y validación de tokens CSRF.</p>
        </div>
    </div>
</body>
</html>
