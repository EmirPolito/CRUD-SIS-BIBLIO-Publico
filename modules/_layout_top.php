<?php
require_once '../config/auth.php';
check_auth();

$pageTitle = "Panel Administrativo";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> | Biblioteca</title>
    <link rel="stylesheet" href="../assets/dashboard/<?= basename($_SERVER['PHP_SELF'], '.php') ?>.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="sidebar-header">
            <i class="fa fa-book-open"></i> SIS-BIBLIO
        </div>
        <div class="user-info">
            <p>Conectado como:</p>
            <strong><?= htmlspecialchars($_SESSION['user_name']) ?></strong>
            <p style="color:#aaa; font-size:11px; margin-top:5px;"><?= $_SESSION['user_role'] == 1 ? 'ADMINISTRADOR' : 'LECTOR REGULAR' ?></p>
        </div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php" class="<?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>"><i class="fa fa-chart-line" style="width:25px;"></i> Inicio</a></li>
            <li><a href="libros.php" class="<?= basename($_SERVER['PHP_SELF']) == 'libros.php' ? 'active' : '' ?>"><i class="fa fa-book" style="width:25px;"></i> Catálogo</a></li>
            <li><a href="prestamos.php" class="<?= basename($_SERVER['PHP_SELF']) == 'prestamos.php' ? 'active' : '' ?>"><i class="fa fa-handshake" style="width:25px;"></i> Mis Préstamos</a></li>
            
            <?php if($_SESSION['user_role'] == 1): ?>
                <li style="margin-top:20px; padding-left:25px; font-size:11px; color:#666; font-weight:bold; letter-spacing:1px;">ADMINISTRACIÓN</li>
                <li><a href="lectores.php" class="<?= basename($_SERVER['PHP_SELF']) == 'lectores.php' ? 'active' : '' ?>"><i class="fa fa-users" style="width:25px;"></i> Lectores</a></li>
                <li><a href="usuarios.php" class="<?= basename($_SERVER['PHP_SELF']) == 'usuarios.php' ? 'active' : '' ?>"><i class="fa fa-user-shield" style="width:25px;"></i> Staff del Sistema</a></li>
            <?php endif; ?>
        </ul>
        <a href="../logout.php" class="btn-logout"><i class="fa fa-sign-out-alt"></i> Salir</a>
    </nav>
    
    <!-- Main Wrapper -->
    <main class="main-content">
        <header class="top-nav">
            <button class="mobile-toggle-btn" onclick="document.querySelector('.sidebar').classList.toggle('active')"><i class="fa fa-bars"></i></button>
            <h2><?= $pageTitle ?></h2>
            <div class="tools">
                <span style="color:#888; font-size:14px;"><?= date('d M Y') ?></span>
            </div>
        </header>

        <section class="page-wrapper">
