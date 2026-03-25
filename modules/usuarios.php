<?php
require_once '../config/auth.php';
require_once '../config/database.php';
require_admin();

$db = new DBConnection();
$pdo = $db->connect();

if (isset($_GET['del']) && verify_csrf($_GET['token'] ?? '')) {
    if ($_GET['del'] != $_SESSION['user_acc_id']) {
        $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id_usuario = ?");
        $stmt->execute([$_GET['del']]);
        header("Location: usuarios.php?msg=deleted");
    } else {
        header("Location: usuarios.php?msg=error");
    }
    exit();
}

$pageTitle = "Gestión Administrativa de Usuarios";
require_once '_layout_top.php';
$csrf = generate_csrf();
?>

<div class="content-card">
    <div style="display:flex; justify-content:space-between; margin-bottom:20px;">
        <h3 style="color:#555;">Usuarios del Sistema Autorizados</h3>
        <a href="usuarios_form.php" class="btn btn-primary"><i class="fa fa-user-plus"></i> Ingresar Usuario</a>
    </div>

    <?php if(isset($_GET['msg'])): ?>
        <?php if($_GET['msg'] == 'deleted'): ?>
            <div class="alert alert-success">Cuenta eliminada de la base de datos de manera definitiva.</div>
        <?php else: ?>
            <div class="alert alert-error">Error: No puedes auto-eliminarte durante tu misma sesión.</div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID Empleado</th>
                    <th>Nombre y Apellidos</th>
                    <th>Correo Electrónico de Acceso</th>
                    <th>Nivel (Rol)</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $q = $pdo->query("SELECT u.*, r.nombre_rol FROM usuarios u JOIN roles r ON u.id_rol = r.id_rol ORDER BY u.id_usuario DESC");
                while($row = $q->fetch()):
                ?>
                <tr>
                    <td><?= $row['id_usuario'] ?></td>
                    <td><strong><?= htmlspecialchars($row['nombre_completo']) ?></strong></td>
                    <td><?= htmlspecialchars($row['correo']) ?></td>
                    <td>
                        <span style="background:<?= $row['id_rol']==1 ? '#e14eca' : '#1d8cf8' ?>; color:#fff; padding:4px 10px; border-radius:12px; font-size:11px; font-weight:bold;">
                            <?= strtoupper($row['nombre_rol']) ?>
                        </span>
                    </td>
                    <td>
                        <a href="usuarios_form.php?id=<?= $row['id_usuario'] ?>" class="btn btn-warning" style="padding:5px 10px; font-size:12px;"><i class="fa fa-edit"></i></a>
                        <?php if($row['id_usuario'] != $_SESSION['user_acc_id']): ?>
                        <a href="usuarios.php?del=<?= $row['id_usuario'] ?>&token=<?= $csrf ?>" class="btn btn-danger" style="padding:5px 10px; font-size:12px;" onclick="return confirm('¿Seguro quieres desvincular y eliminar a este empleado del sistema?');"><i class="fa fa-trash"></i></a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '_layout_bottom.php'; ?>
