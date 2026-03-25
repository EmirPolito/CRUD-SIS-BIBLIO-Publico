<?php
require_once '../config/auth.php';
require_once '../config/database.php';
check_auth();

$db = new DBConnection();
$pdo = $db->connect();

if (isset($_GET['del']) && verify_csrf($_GET['token'] ?? '')) {
    if ($_SESSION['user_role'] == 1) {
        $stmt = $pdo->prepare("DELETE FROM prestamos WHERE id_prestamo = ?");
        $stmt->execute([$_GET['del']]);
        header("Location: prestamos.php?msg=deleted");
        exit();
    }
}

if (isset($_GET['cancel']) && verify_csrf($_GET['token'] ?? '')) {
    $stmt = $pdo->prepare("UPDATE prestamos SET estado = 'Cancelada' WHERE id_prestamo = ?");
    $stmt->execute([$_GET['cancel']]);
    header("Location: prestamos.php?msg=cancelled");
    exit();
}

$pageTitle = "Gestión de Préstamos";
require_once '_layout_top.php';
$csrf = generate_csrf();
?>

<div class="content-card">
    <div style="display:flex; justify-content:space-between; margin-bottom:20px;">
        <h3 style="color:#555;">Historial de Préstamos</h3>
        <a href="prestamos_form.php" class="btn btn-primary"><i class="fa fa-plus"></i> Registrar Préstamo</a>
    </div>

    <?php if(isset($_GET['msg'])): ?>
        <div class="alert alert-success">
            <?= $_GET['msg'] == 'deleted' ? 'Préstamo eliminado de la base de datos.' : 'El préstamo ha sido cancelado exitosamente.' ?>
        </div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Lector</th>
                    <th>Libro</th>
                    <th>F. Salida</th>
                    <th>F. Límite/Devolución</th>
                    <th>Estatus</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $uid = $_SESSION['user_acc_id'];
                if ($_SESSION['user_role'] == 1) {
                    $q = $pdo->query("SELECT p.*, l.nombre_completo AS lector_nombre, b.titulo AS libro_tit FROM prestamos p JOIN lectores l ON p.id_lector = l.id_lector JOIN libros b ON p.id_libro = b.id_libro ORDER BY p.id_prestamo DESC");
                } else {
                    $q = $pdo->prepare("SELECT p.*, l.nombre_completo AS lector_nombre, b.titulo AS libro_tit FROM prestamos p JOIN lectores l ON p.id_lector = l.id_lector JOIN libros b ON p.id_libro = b.id_libro WHERE l.id_usuario = ? ORDER BY p.id_prestamo DESC");
                    $q->execute([$uid]);
                }
                while($row = $q->fetch()):
                ?>
                <tr>
                    <td><strong><?= htmlspecialchars($row['codigo']) ?></strong></td>
                    <td><?= htmlspecialchars($row['lector_nombre']) ?></td>
                    <td><?= htmlspecialchars($row['libro_tit']) ?></td>
                    <td><?= $row['fecha_prestamo'] ?></td>
                    <td><?= $row['fecha_devolucion'] ?></td>
                    <td>
                        <?php 
                        $colors = ['Pendiente'=>'#ff8d72', 'Confirmada'=>'#00f2c3', 'Cancelada'=>'#e3e3e3'];
                        $textColors = ['Pendiente'=>'#fff', 'Confirmada'=>'#fff', 'Cancelada'=>'#555'];
                        $st = $row['estado']; 
                        ?>
                        <span style="background:<?= $colors[$st] ?>; color:<?= $textColors[$st] ?>; padding:4px 10px; border-radius:12px; font-size:11px; font-weight:bold;">
                            <?= strtoupper($st) ?>
                        </span>
                    </td>
                    <td>
                        <?php if($_SESSION['user_role'] == 1): ?>
                            <a href="prestamos_form.php?id=<?= $row['id_prestamo'] ?>" class="btn btn-warning" style="padding:5px 10px; font-size:12px;"><i class="fa fa-edit"></i></a>
                            <a href="prestamos.php?del=<?= $row['id_prestamo'] ?>&token=<?= $csrf ?>" class="btn btn-danger" style="padding:5px 10px; font-size:12px;" onclick="return confirm('¿Borrar definitivamente?');"><i class="fa fa-trash"></i></a>
                        <?php endif; ?>
                        <?php if($st == 'Pendiente' || $st == 'Confirmada'): ?>
                            <a href="prestamos.php?cancel=<?= $row['id_prestamo'] ?>&token=<?= $csrf ?>" class="btn btn-secondary" style="background:#555; padding:5px 10px; font-size:12px;" onclick="return confirm('¿Cancelar préstamo?');"><i class="fa fa-ban"></i></a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '_layout_bottom.php'; ?>
