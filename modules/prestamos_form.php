<?php
require_once '../config/auth.php';
require_once '../config/database.php';
check_auth();

$db = new DBConnection();
$pdo = $db->connect();

$id = $_GET['id'] ?? null;
if ($id && $_SESSION['user_role'] != 1) {
    die("Solo los administradores pueden editar préstamos pasados. Los lectores solo pueden crearlos o cancelarlos.");
}

$prestamo = ['codigo'=>'LIB-'.mt_rand(1000,9999), 'id_lector'=>'', 'id_libro'=>'', 'fecha_prestamo'=>date('Y-m-d'), 'fecha_devolucion'=>date('Y-m-d', strtotime('+7 days')), 'estado'=>'Pendiente'];

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM prestamos WHERE id_prestamo = ?");
    $stmt->execute([$id]);
    $prestamo = $stmt->fetch() ?: $prestamo;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf'] ?? '')) die("CSRF Failed");

    $cod = trim($_POST['codigo']);
    $lec = intval($_POST['id_lector']);
    $lib = intval($_POST['id_libro']);
    $f_p = $_POST['fecha_prestamo'];
    $f_d = $_POST['fecha_devolucion'];
    $est = $_POST['estado'] ?? 'Pendiente';

    if ($id) {
        $upd = $pdo->prepare("UPDATE prestamos SET id_lector=?, id_libro=?, fecha_prestamo=?, fecha_devolucion=?, estado=? WHERE id_prestamo=?");
        $upd->execute([$lec, $lib, $f_p, $f_d, $est, $id]);
    } else {
        $ins = $pdo->prepare("INSERT INTO prestamos (codigo, id_lector, id_libro, fecha_prestamo, fecha_devolucion, estado) VALUES (?, ?, ?, ?, ?, ?)");
        $ins->execute([$cod, $lec, $lib, $f_p, $f_d, $est]);
    }
    header("Location: prestamos.php");
    exit();
}

$pageTitle = $id ? "Editar Préstamo" : "Registrar Salida de Libro";
require_once '_layout_top.php';

// Dropdowns for books
$libros = $pdo->query("SELECT id_libro, titulo, autor FROM libros WHERE estado = 'Disponible' OR id_libro = '{$prestamo['id_libro']}'")->fetchAll();

// Dropdowns for readers - filter if user is not admin
if ($_SESSION['user_role'] == 1) {
    $lectores = $pdo->query("SELECT id_lector, nombre_completo FROM lectores WHERE estado = 'Activo'")->fetchAll();
} else {
    $lectores = $pdo->prepare("SELECT id_lector, nombre_completo FROM lectores WHERE id_usuario = ? AND estado = 'Activo'");
    $lectores->execute([$_SESSION['user_acc_id']]);
    $lectores = $lectores->fetchAll();
}
?>

<div class="content-card" style="max-width: 600px; margin: 0 auto;">
    <h3 style="margin-bottom:20px; color:#555;"><?= $pageTitle ?></h3>
    <?php if(empty($lectores)): ?>
        <div class="alert alert-error">Error: No tienes una ficha de Lector Activa vinculada. Solicita a un administrador que te registre como Lector Activo antes de pedir un libro.</div>
    <?php else: ?>
    <form method="POST">
        <input type="hidden" name="csrf" value="<?= generate_csrf() ?>">
        <input type="hidden" name="codigo" value="<?= htmlspecialchars($prestamo['codigo']) ?>">
        
        <div class="form-group">
            <label>Código Generado</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($prestamo['codigo']) ?>" disabled>
        </div>

        <div class="form-group">
            <label>Lector Solicitante</label>
            <select name="id_lector" class="form-control" required>
                <?php foreach($lectores as $l): ?>
                    <option value="<?= $l['id_lector'] ?>" <?= $prestamo['id_lector']==$l['id_lector']?'selected':'' ?>>
                        <?= htmlspecialchars($l['nombre_completo']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Libro a Prestar</label>
            <select name="id_libro" class="form-control" required>
                <?php foreach($libros as $b): ?>
                    <option value="<?= $b['id_libro'] ?>" <?= $prestamo['id_libro']==$b['id_libro']?'selected':'' ?>>
                        <?= htmlspecialchars($b['titulo']) ?> (<?= htmlspecialchars($b['autor']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="display:flex; gap:15px;">
            <div class="form-group" style="flex:1;">
                <label>F. Préstamo</label>
                <input type="date" name="fecha_prestamo" class="form-control" value="<?= $prestamo['fecha_prestamo'] ?>" required>
            </div>
            <div class="form-group" style="flex:1;">
                <label>F. Límite/Devolución</label>
                <input type="date" name="fecha_devolucion" class="form-control" value="<?= $prestamo['fecha_devolucion'] ?>" required>
            </div>
        </div>

        <?php if($_SESSION['user_role'] == 1 && $id): ?>
        <div class="form-group">
            <label>Estado del Préstamo</label>
            <select name="estado" class="form-control" required>
                <option value="Pendiente" <?= $prestamo['estado']=='Pendiente'?'selected':'' ?>>Pendiente / Activo</option>
                <option value="Confirmada" <?= $prestamo['estado']=='Confirmada'?'selected':'' ?>>Devuelto (Confirmada)</option>
                <option value="Cancelada" <?= $prestamo['estado']=='Cancelada'?'selected':'' ?>>Cancelada / Extraviado</option>
            </select>
        </div>
        <?php endif; ?>
        
        <div style="margin-top:20px; display:flex; gap:10px;">
            <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Generar Vale</button>
            <a href="prestamos.php" class="btn btn-secondary" style="background:#888;">Cancelar / Volver</a>
        </div>
    </form>
    <?php endif; ?>
</div>

<?php require_once '_layout_bottom.php'; ?>
