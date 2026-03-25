<?php
require_once 'config/database.php';
require_once 'config/auth.php';

$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf'] ?? '';
    if (!verify_csrf($token)) {
        $error_msg = "Error de validación de seguridad CSRF.";
    }
    else {
        $email = trim($_POST['email']);
        $pass = trim($_POST['pwd']);

        $db = new DBConnection();
        $pdo = $db->connect();

        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE correo = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            // Check lockout
            if ($user['bloqueado_hasta'] && new DateTime() < new DateTime($user['bloqueado_hasta'])) {
                $error_msg = "Cuenta congelada temporalmente por intentos fallidos.";
            }
            else {
                if (password_verify($pass, $user['password'])) {
                    // Reset tries
                    $pdo->prepare("UPDATE usuarios SET intentos_fallidos = 0, bloqueado_hasta = NULL WHERE id_usuario = ?")->execute([$user['id_usuario']]);
                    session_regenerate_id(true);
                    $_SESSION['user_acc_id'] = $user['id_usuario'];
                    $_SESSION['user_name'] = $user['nombre_completo'];
                    $_SESSION['user_role'] = $user['id_rol'];
                    header("Location: modules/dashboard.php");
                    exit();
                }
                else {
                    $fails = $user['intentos_fallidos'] + 1;
                    $lock = NULL;
                    if ($fails >= 3) {
                        $lock = date('Y-m-d H:i:s', strtotime('+15 minutes'));
                        $error_msg = "Demasiados intentos. Cuenta bloqueada por 15 minutos.";
                    }
                    else {
                        $error_msg = "Credenciales incorrectas.";
                    }
                    $pdo->prepare("UPDATE usuarios SET intentos_fallidos = ?, bloqueado_hasta = ? WHERE id_usuario = ?")->execute([$fails, $lock, $user['id_usuario']]);
                }
            }
        }
        else {
            $error_msg = "Credenciales incorrectas.";
        }
    }
}
$csrf_token = generate_csrf();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso al Sistema | Biblioteca</title>
    <link rel="stylesheet" href="assets/login.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <a href="index.php" style="position:absolute; top:20px; left:20px; color:white; text-decoration:none; font-size:14px; font-weight:600; background:rgba(0,0,0,0.5); padding:10px 18px; border-radius:30px; backdrop-filter:blur(4px); border:1px solid rgba(255,255,255,0.2); transition:all 0.3s;" onmouseover="this.style.background='rgba(0,0,0,0.8)'" onmouseout="this.style.background='rgba(0,0,0,0.5)'">
        <i class="fa fa-home"></i> Volver al Inicio
    </a>
    <div class="login-box">
        <h2>Ingreso al Sistema</h2>
        <?php if ($error_msg): ?>
            <div class="alert"><?php echo htmlspecialchars($error_msg); ?></div>
        <?php
endif; ?>
        <form method="POST" action="">
            <input type="hidden" name="csrf" value="<?php echo $csrf_token; ?>">
            <input type="email" name="email" class="form-control" placeholder="Correo electrónico" required autofocus>
            <div style="position:relative;">
                <input type="password" name="pwd" id="pwd-login" class="form-control" placeholder="Contraseña" required>
                <i class="fa fa-eye-slash" id="toggle-pwd" style="position:absolute; right:15px; top:15px; cursor:pointer; color:#666;"></i>
            </div>
            <button type="submit" class="btn-submit">Ingresar</button>
        </form>
        <div style="text-align:center; margin-top:20px; font-size:14px;">
    ¿No tienes cuenta? 
    <a href="register.php"
       style="color:#2563eb; text-decoration:none;"
       onmouseover="this.style.textDecoration='underline'"
       onmouseout="this.style.textDecoration='none'">
       Regístrate aquí
    </a><br><br>
    
    <a href="recover.php"
       style="color:#2563eb; text-decoration:none; font-size:14px;"
       onmouseover="this.style.textDecoration='underline'"
       onmouseout="this.style.textDecoration='none'">
       Olvidé mi contraseña
    </a>
</div>

    </div>
    <script>
    // Validación del cliente con manipulación nativa del DOM (Requisito 3.1 y 2.1)
    document.addEventListener("DOMContentLoaded", function() {
        const form = document.querySelector("form");
        form.addEventListener("submit", function(event) {
            const pwd = document.querySelector("input[name='pwd']").value;
            if (pwd.length > 0 && pwd.length < 5) {
                event.preventDefault();
                let errorDiv = document.getElementById("js-error-msg");
                if (!errorDiv) {
                    errorDiv = document.createElement("div");
                    errorDiv.id = "js-error-msg";
                    errorDiv.className = "alert";
                    errorDiv.style.background = "#fee2e2";
                    errorDiv.style.color = "#991b1b";
                    // Manipulación del DOM mendiante APIs nativas
                    form.insertBefore(errorDiv, form.firstChild); 
                }
                errorDiv.innerText = "Validación JS: La contraseña debe tener al menos 5 caracteres.";
            }
        });

        // Ver y esconder contraseña
        const togglePwd = document.getElementById('toggle-pwd');
        const pwdInput = document.getElementById('pwd-login');
        togglePwd.addEventListener('click', function() {
            const isPassword = pwdInput.getAttribute('type') === 'password';
            pwdInput.setAttribute('type', isPassword ? 'text' : 'password');
            this.classList.toggle('fa-eye', isPassword);
            this.classList.toggle('fa-eye-slash', !isPassword);
        });
    });
    </script>
</body>
</html>
