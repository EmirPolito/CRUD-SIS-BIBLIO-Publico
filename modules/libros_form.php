<?php
require_once '../config/auth.php';
require_once '../config/database.php';
require_admin();

$db = new DBConnection();
$pdo = $db->connect();

$id = $_GET['id'] ?? null;
$libro = ['titulo'=>'', 'autor'=>'', 'precio'=>'', 'estado'=>'Disponible'];

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM libros WHERE id_libro = ?");
    $stmt->execute([$id]);
    $libro = $stmt->fetch() ?: $libro;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf'] ?? '')) die("CSRF Failed");

    $tit = trim($_POST['titulo']);
    $aut = trim($_POST['autor']);
    $prc = floatval($_POST['precio']);
    $est = $_POST['estado'];

    if ($id) {
        $upd = $pdo->prepare("UPDATE libros SET titulo=?, autor=?, precio=?, estado=? WHERE id_libro=?");
        $upd->execute([$tit, $aut, $prc, $est, $id]);
    } else {
        $ins = $pdo->prepare("INSERT INTO libros (titulo, autor, precio, estado) VALUES (?, ?, ?, ?)");
        $ins->execute([$tit, $aut, $prc, $est]);
    }
    header("Location: libros.php");
    exit();
}

$pageTitle = $id ? "Editar Libro" : "Añadir Nuevo Libro";
require_once '_layout_top.php';
?>

<div class="content-card" style="max-width: 600px; margin: 0 auto;">
    <h3 style="margin-bottom:20px; color:#555;"><?= $pageTitle ?></h3>
    <form method="POST">
        <input type="hidden" name="csrf" value="<?= generate_csrf() ?>">
        
        <div class="form-group">
            <label>Título de la Obra</label>
            <input type="text" name="titulo" class="form-control" value="<?= htmlspecialchars($libro['titulo']) ?>" required>
        </div>
        <div class="form-group">
            <label>Autor / Escritor</label>
            <input type="text" name="autor" class="form-control" value="<?= htmlspecialchars($libro['autor']) ?>" required>
        </div>
        <div class="form-group">
            <label>Precio Estimado (Reposición)</label>
            <input type="number" step="0.01" name="precio" class="form-control" value="<?= $libro['precio'] ?>" required>
        </div>
        <div class="form-group">
            <label>Estado Físico/Disponibilidad</label>
            <select name="estado" class="form-control" required>
                <option value="Disponible" <?= $libro['estado']=='Disponible'?'selected':'' ?>>Disponible</option>
                <option value="Ocupada" <?= $libro['estado']=='Ocupada'?'selected':'' ?>>Ocupada (En préstamo)</option>
                <option value="Mantenimiento" <?= $libro['estado']=='Mantenimiento'?'selected':'' ?>>Mantenimiento (Reparación)</option>
            </select>
        </div>
        
        <div style="margin-top:30px; display:flex; gap:10px;">
            <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Guardar Cambios</button>
            <a href="libros.php" class="btn btn-secondary" style="background:#888;">Cancelar</a>
        </div>
    </form>
</div>

<?php require_once '_layout_bottom.php'; ?>
