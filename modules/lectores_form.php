<?php
require_once '../config/auth.php';
require_once '../config/database.php';
require_admin();

$db = new DBConnection();
$pdo = $db->connect();

$id = $_GET['id'] ?? null;
$lector = ['nombre_completo'=>'', 'telefono'=>'', 'estado'=>'Activo', 'id_usuario'=>null];

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM lectores WHERE id_lector = ?");
    $stmt->execute([$id]);
    $lector = $stmt->fetch() ?: $lector;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf'] ?? '')) die("CSRF Failed");

    $nom = trim($_POST['nombre']);
    $tel = trim($_POST['telefono']);
    $est = $_POST['estado'];
    $usr = !empty($_POST['id_usuario']) ? $_POST['id_usuario'] : null;

    if ($id) {
        $upd = $pdo->prepare("UPDATE lectores SET nombre_completo=?, telefono=?, estado=?, id_usuario=? WHERE id_lector=?");
        $upd->execute([$nom, $tel, $est, $usr, $id]);
    } else {
        $ins = $pdo->prepare("INSERT INTO lectores (nombre_completo, telefono, estado, id_usuario) VALUES (?, ?, ?, ?)");
        $ins->execute([$nom, $tel, $est, $usr]);
    }
    header("Location: lectores.php");
    exit();
}

$pageTitle = $id ? "Modificar Ficha de Lector" : "Registrar Nuevo Lector";
require_once '_layout_top.php';

// Get user accounts that are clients for linking
$usersQuery = $pdo->query("SELECT id_usuario, correo FROM usuarios WHERE id_rol = 2");
$usersLinked = $usersQuery->fetchAll();
?>

<div class="content-card" style="max-width: 600px; margin: 0 auto;">
    <h3 style="margin-bottom:20px; color:#555;"><?= $pageTitle ?></h3>
    <form method="POST">
        <input type="hidden" name="csrf" value="<?= generate_csrf() ?>">
        
        <div class="form-group">
            <label>Nombre Completo</label>
            <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($lector['nombre_completo']) ?>" required>
        </div>
        <div class="form-group">
            <label>Teléfono / Contacto</label>
            <input type="text" name="telefono" class="form-control" value="<?= htmlspecialchars($lector['telefono']) ?>" required>
        </div>
        <div class="form-group">
            <label>Estado de la Afiliación</label>
            <select name="estado" class="form-control" required>
                <option value="Activo" <?= $lector['estado']=='Activo'?'selected':'' ?>>Afiliación Activa</option>
                <option value="Inactivo" <?= $lector['estado']=='Inactivo'?'selected':'' ?>>Suspendido / Inactivo</option>
            </select>
        </div>
        <div class="form-group">
            <label>Vincular a Cuenta de Acceso (Opcional)</label>
            <select name="id_usuario" class="form-control">
                <option value="">-- Sin cuenta web asocidada --</option>
                <?php foreach($usersLinked as $u): ?>
                    <option value="<?= $u['id_usuario'] ?>" <?= $lector['id_usuario']==$u['id_usuario']?'selected':'' ?>>
                        <?= htmlspecialchars($u['correo']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <small style="color:#888;">Permite al usuario loguearse y ver sus préstamos propios.</small>
        </div>
        
        <div style="margin-top:30px; display:flex; gap:10px;">
            <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Guardar Ficha</button>
            <a href="lectores.php" class="btn btn-secondary" style="background:#888;">Cancelar</a>
        </div>
    </form>
</div>

<?php require_once '_layout_bottom.php'; ?>
