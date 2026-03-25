<?php
require_once '../config/auth.php';
require_once '../config/database.php';
check_auth();

$db = new DBConnection();
$pdo = $db->connect();

// Fetch stat sums completely differently from old project
$stats = [
    'libros' => $pdo->query("SELECT COUNT(*) FROM libros")->fetchColumn(),
    'prestamos_activos' => $pdo->query("SELECT COUNT(*) FROM prestamos WHERE estado != 'Cancelada'")->fetchColumn()
];

if ($_SESSION['user_role'] == 1) {
    $stats['lectores'] = $pdo->query("SELECT COUNT(*) FROM lectores")->fetchColumn();
} else {
    $stats['mis_prestamos'] = $pdo->query("SELECT COUNT(*) FROM prestamos p JOIN lectores l ON p.id_lector=l.id_lector WHERE l.id_usuario = {$_SESSION['user_acc_id']}")->fetchColumn();
}

require_once '_layout_top.php'; 
?>

<div class="stats-grid">
    <div class="stat-card">
        <h3>Total Libros Catálogo</h3>
        <p><?= $stats['libros'] ?></p>
    </div>
    <?php if($_SESSION['user_role'] == 1): ?>
    <div class="stat-card">
        <h3>Lectores Registrados</h3>
        <p><?= $stats['lectores'] ?></p>
    </div>
    <div class="stat-card">
        <h3>Préstamos Activos (Global)</h3>
        <p><?= $stats['prestamos_activos'] ?></p>
    </div>
    <?php else: ?>
    <div class="stat-card">
        <h3>Mis Préstamos Históricos</h3>
        <p><?= $stats['mis_prestamos'] ?></p>
    </div>
    <?php endif; ?>
</div>

<div class="content-card">
    <h3 style="margin-bottom:15px; color:#444;">Bienvenido a la Plataforma</h3>
    <p style="color:#666; line-height:1.6;">
        Utilice el menú lateral para acceder a las opciones y gestionar la información. <br>
        Recuerde mantener su sesión segura y cerrarla al finalizar su jornada.
    </p>
</div>

<?php require_once '_layout_bottom.php'; ?>
