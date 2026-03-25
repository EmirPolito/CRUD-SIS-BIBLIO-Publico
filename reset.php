<?php
session_start();
require_once 'config/database.php';
require_once 'config/auth.php';

$t = $_GET['t'] ?? ($_POST['token'] ?? '');
if (empty($t)) die("Token faltante.");

$db = new DBConnection();
$pdo = $db->connect();

$stmt = $pdo->prepare("SELECT id_usuario, reset_expires FROM usuarios WHERE reset_token = ?");
$stmt->execute([$t]);
$user = $stmt->fetch();

if (!$user || new DateTime() > new DateTime($user['reset_expires'])) {
    die("El token de seguridad es inválido o expiró.");
}

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf'] ?? '')) die("Error CSRF");
    
    $pwd = $_POST['pwd'];
    if (strlen($pwd) >= 5) {
        $hash = password_hash($pwd, PASSWORD_DEFAULT);
        $pdo->prepare("UPDATE usuarios SET password=?, reset_token=NULL, reset_expires=NULL, intentos_fallidos=0, bloqueado_hasta=NULL WHERE id_usuario=?")->execute([$hash, $user['id_usuario']]);
        $msg = "<div class='alert alert-success'>Contraseña actualizada.<br><br><a href='login.php' style='color:#166534'>Ir al Login</a></div>";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Contraseña | Biblioteca</title>
    <link rel="stylesheet" href="assets/reset.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <a href="index.php" style="position:absolute; top:20px; left:20px; color:white; text-decoration:none; font-size:14px; font-weight:600; background:rgba(0,0,0,0.5); padding:10px 18px; border-radius:30px; backdrop-filter:blur(4px); border:1px solid rgba(255,255,255,0.2); transition:all 0.3s;" onmouseover="this.style.background='rgba(0,0,0,0.8)'" onmouseout="this.style.background='rgba(0,0,0,0.5)'">
        <i class="fa fa-home"></i> Volver al Inicio
    </a>
    <div class="login-box">
        <h2 style="text-align:center;">Crear Nueva Clave</h2>
        <?= $msg ?>
        <?php if(!$msg): ?>
        <form method="POST">
            <input type="hidden" name="csrf" value="<?= generate_csrf() ?>">
            <input type="hidden" name="token" value="<?= htmlspecialchars($t) ?>">
            <div style="position:relative;">
                <input type="password" name="pwd" id="pwd-reset" class="form-control" placeholder="Nueva contraseña secreta" required minlength="5">
                <i class="fa fa-eye-slash" id="toggle-pwd-res" style="position:absolute; right:15px; top:15px; cursor:pointer; color:#666;"></i>
            </div>
            <button type="submit" class="btn-submit">Actualizar y Entrar</button>
        </form>
        <?php endif; ?>

    </div>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const togglePwd = document.getElementById('toggle-pwd-res');
        const pwdInput = document.getElementById('pwd-reset');
        if (togglePwd && pwdInput) {
            togglePwd.addEventListener('click', function() {
                const isPassword = pwdInput.getAttribute('type') === 'password';
                pwdInput.setAttribute('type', isPassword ? 'text' : 'password');
                this.classList.toggle('fa-eye', isPassword);
                this.classList.toggle('fa-eye-slash', !isPassword);
            });
        }
    });
    </script>
</body>
</html>
