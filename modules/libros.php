<?php
require_once '../config/auth.php';
require_once '../config/database.php';
check_auth();

$db = new DBConnection();
$pdo = $db->connect();

// Handling deletion
if (isset($_GET['del']) && verify_csrf($_GET['token'] ?? '')) {
    if ($_SESSION['user_role'] == 1) {
        $stmt = $pdo->prepare("DELETE FROM libros WHERE id_libro = ?");
        $stmt->execute([$_GET['del']]);
        header("Location: libros.php?msg=deleted");
        exit();
    }
}

$pageTitle = "Gestión de Catálogo (Libros)";
require_once '_layout_top.php';
$csrf = generate_csrf();
?>

<div class="content-card">
    <div style="display:flex; justify-content:space-between; margin-bottom:20px;">
        <h3 style="color:#555;">Lista de Libros</h3>
        <?php if($_SESSION['user_role'] == 1): ?>
        <a href="libros_form.php" class="btn btn-primary"><i class="fa fa-plus"></i> Nuevo Libro</a>
        <?php endif; ?>
    </div>
    
    <?php if(isset($_GET['msg']) && $_GET['msg']=='deleted'): ?>
        <div class="alert alert-success">Libro eliminado correctamente.</div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Título del Libro</th>
                    <th>Autor / Creador</th>
                    <th>Precio de Reposición</th>
                    <th>Estado</th>
                    <?php if($_SESSION['user_role'] == 1): ?><th>Acciones</th><?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php
                $q = $pdo->query("SELECT * FROM libros ORDER BY id_libro DESC");
                while($row = $q->fetch()):
                ?>
                <tr>
                    <td><?= $row['id_libro'] ?></td>
                    <td><strong><?= htmlspecialchars($row['titulo']) ?></strong></td>
                    <td><?= htmlspecialchars($row['autor']) ?></td>
                    <td>$<?= number_format($row['precio'], 2) ?></td>
                    <td>
                        <?php 
                        $colors = ['Disponible'=>'#00f2c3', 'Ocupada'=>'#fd5d93', 'Mantenimiento'=>'#ff8d72']; 
                        $color = $colors[$row['estado']] ?? '#888';
                        ?>
                        <span style="background:<?= $color ?>; color:#fff; padding:4px 10px; border-radius:12px; font-size:11px; font-weight:bold;">
                            <?= strtoupper($row['estado']) ?>
                        </span>
                    </td>
                    <?php if($_SESSION['user_role'] == 1): ?>
                    <td>
                        <a href="libros_form.php?id=<?= $row['id_libro'] ?>" class="btn btn-warning" style="padding:5px 10px; font-size:12px;"><i class="fa fa-edit"></i></a>
                        <a href="libros.php?del=<?= $row['id_libro'] ?>&token=<?= $csrf ?>" class="btn btn-danger" style="padding:5px 10px; font-size:12px;" onclick="return confirm('¿Seguro que deseas eliminar este registro?');"><i class="fa fa-trash"></i></a>
                    </td>
                    <?php endif; ?>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '_layout_bottom.php'; ?>
