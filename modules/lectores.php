<?php
require_once '../config/auth.php';
require_once '../config/database.php';
require_admin();

$db = new DBConnection();
$pdo = $db->connect();

if (isset($_GET['del']) && verify_csrf($_GET['token'] ?? '')) {
    $stmt = $pdo->prepare("DELETE FROM lectores WHERE id_lector = ?");
    $stmt->execute([$_GET['del']]);
    header("Location: lectores.php?msg=deleted");
    exit();
}

$pageTitle = "Gestión de Lectores";
require_once '_layout_top.php';
$csrf = generate_csrf();
?>

<div class="content-card">
    <div style="display:flex; justify-content:space-between; margin-bottom:20px;">
        <h3 style="color:#555;">Directorio de Lectores</h3>
        <a href="lectores_form.php" class="btn btn-primary"><i class="fa fa-user-plus"></i> Añadir Lector</a>
    </div>
    
    <?php if(isset($_GET['msg']) && $_GET['msg']=='deleted'): ?>
        <div class="alert alert-success">Lector borrado con éxito.</div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre Completo</th>
                    <th>Teléfono</th>
                    <th>Afiliación</th>
                    <th>Cta. Asignada (Usuario)</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $q = $pdo->query("SELECT l.*, u.correo FROM lectores l LEFT JOIN usuarios u ON l.id_usuario = u.id_usuario ORDER BY l.id_lector DESC");
                while($row = $q->fetch()):
                ?>
                <tr>
                    <td><?= $row['id_lector'] ?></td>
                    <td><strong><?= htmlspecialchars($row['nombre_completo']) ?></strong></td>
                    <td><?= htmlspecialchars($row['telefono']) ?></td>
                    <td>
                        <?php if($row['estado'] == 'Activo'): ?>
                            <span style="color:#2e7d32; font-weight:bold;"><i class="fa fa-check-circle"></i> Activo</span>
                        <?php else: ?>
                            <span style="color:#c62828; font-weight:bold;"><i class="fa fa-times-circle"></i> Inactivo</span>
                        <?php endif; ?>
                    </td>
                    <td><?= $row['correo'] ? htmlspecialchars($row['correo']) : '<em>Sin cuenta</em>' ?></td>
                    <td>
                        <a href="lectores_form.php?id=<?= $row['id_lector'] ?>" class="btn btn-warning" style="padding:5px 10px; font-size:12px;"><i class="fa fa-edit"></i></a>
                        <a href="lectores.php?del=<?= $row['id_lector'] ?>&token=<?= $csrf ?>" class="btn btn-danger" style="padding:5px 10px; font-size:12px;" onclick="return confirm('Al eliminar al lector, se borrará su historial de préstamos asociado (Cascae). ¿Continuar?');"><i class="fa fa-trash"></i></a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '_layout_bottom.php'; ?>
