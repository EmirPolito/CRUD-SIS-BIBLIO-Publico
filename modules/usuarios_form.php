<?php
require_once '../config/auth.php';
require_once '../config/database.php';
require_admin();

$db = new DBConnection();
$pdo = $db->connect();

$id = $_GET['id'] ?? null;
$usr = ['nombre_completo'=>'', 'correo'=>'', 'id_rol'=>2];

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id_usuario = ?");
    $stmt->execute([$id]);
    $usr = $stmt->fetch() ?: $usr;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf'] ?? '')) die("CSRF Failed");

    $nom = trim($_POST['nombre']);
    $cor = trim($_POST['correo']);
    $rol = intval($_POST['rol']);
    $pwd = $_POST['password'];

    if ($id) {
        if (!empty($pwd)) {
            $hsh = password_hash($pwd, PASSWORD_DEFAULT);
            $upd = $pdo->prepare("UPDATE usuarios SET nombre_completo=?, correo=?, id_rol=?, password=? WHERE id_usuario=?");
            $upd->execute([$nom, $cor, $rol, $hsh, $id]);
        } else {
            $upd = $pdo->prepare("UPDATE usuarios SET nombre_completo=?, correo=?, id_rol=? WHERE id_usuario=?");
            $upd->execute([$nom, $cor, $rol, $id]);
        }
    } else {
        $hsh = password_hash($pwd, PASSWORD_DEFAULT);
        $ins = $pdo->prepare("INSERT INTO usuarios (nombre_completo, correo, password, id_rol) VALUES (?, ?, ?, ?)");
        $ins->execute([$nom, $cor, $hsh, $rol]);
    }
    header("Location: usuarios.php");
    exit();
}

$pageTitle = $id ? "Editar Propiedades de Cuenta" : "Crear Alta de Empleado";
require_once '_layout_top.php';
$roles = $pdo->query("SELECT * FROM roles")->fetchAll();
?>

<div class="content-card" style="max-width: 600px; margin: 0 auto;">
    <h3 style="margin-bottom:20px; color:#555;"><?= $pageTitle ?></h3>
    <form method="POST">
        <input type="hidden" name="csrf" value="<?= generate_csrf() ?>">
        
        <div class="form-group">
            <label>Nombre y Apellidos</label>
            <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($usr['nombre_completo']) ?>" required autocomplete="off">
        </div>
        <div class="form-group">
            <label>Dirección de Correo (Login)</label>
            <input type="email" name="correo" class="form-control" value="<?= htmlspecialchars($usr['correo']) ?>" required autocomplete="off">
        </div>
        
        <div class="form-group">
            <label>Asignación de Rol de Seguridad</label>
            <select name="rol" class="form-control" required>
                <?php foreach($roles as $r): ?>
                    <option value="<?= $r['id_rol'] ?>" <?= $usr['id_rol']==$r['id_rol']?'selected':'' ?>><?= htmlspecialchars($r['nombre_rol']) ?></option>
                <?php endforeach; ?>
            </select>
            <small style="color:#888; display:block; margin-top:5px;">Un administrador controla todo. Un 'Lector' o rol 2 solo tendrá acceso al menú básico.</small>
        </div>

        <div class="form-group">
            <label>Contraseña <?= $id ? '(Opcional. Dejar en blanco para conservar actual)' : '' ?></label>
            <input type="password" name="password" class="form-control" <?= $id ? '' : 'required' ?> autocomplete="off">
        </div>
        
        <div style="margin-top:30px; display:flex; gap:10px;">
            <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Generar Accesos</button>
            <a href="usuarios.php" class="btn btn-secondary" style="background:#888;">Cancelar y Volver</a>
        </div>
    </form>
</div>

<?php require_once '_layout_bottom.php'; ?>
