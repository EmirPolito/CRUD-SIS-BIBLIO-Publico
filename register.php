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
        $nombre = trim($_POST['nombre']);
        $email = trim($_POST['email']);
        $pass = $_POST['pwd'];

        $db = new DBConnection();
        $pdo = $db->connect();

        $stmt = $pdo->prepare("SELECT id_usuario FROM usuarios WHERE correo = ?");
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $error_msg = "El correo ya está registrado en el sistema.";
        }
        else {
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            $ins = $pdo->prepare("INSERT INTO usuarios (nombre_completo, correo, password, id_rol) VALUES (?, ?, ?, 2)");
            if ($ins->execute([$nombre, $email, $hash])) {
                $new_id = $pdo->lastInsertId();

                // Generar automáticamente su "Carnet de Lector" vinculado a su ID de Usuario
                $ins_lec = $pdo->prepare("INSERT INTO lectores (id_usuario, nombre_completo, telefono, estado) VALUES (?, ?, 'Sin definir', 'Activo')");
                $ins_lec->execute([$new_id, $nombre]);

                session_regenerate_id(true);
                $_SESSION['user_acc_id'] = $new_id;
                $_SESSION['user_name'] = $nombre;
                $_SESSION['user_role'] = 2; // Rol 2: Lector
                header("Location: modules/dashboard.php");
                exit();
            }
            else {
                $error_msg = "Error interno al crear el registro.";
            }
        }
    }
}
$csrf_token = generate_csrf();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro | Biblioteca</title>
    <link rel="stylesheet" href="assets/register.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <a href="index.php" style="position:absolute; top:20px; left:20px; color:white; text-decoration:none; font-size:14px; font-weight:600; background:rgba(0,0,0,0.5); padding:10px 18px; border-radius:30px; backdrop-filter:blur(4px); border:1px solid rgba(255,255,255,0.2); transition:all 0.3s;" onmouseover="this.style.background='rgba(0,0,0,0.8)'" onmouseout="this.style.background='rgba(0,0,0,0.5)'">
        <i class="fa fa-home"></i> Volver al Inicio
    </a>
    <div class="login-box">
        <h2>Crear Cuenta</h2>
        <?php if ($error_msg): ?>
            <div class="alert"><?php echo htmlspecialchars($error_msg); ?></div>
        <?php
endif; ?>
        <form method="POST" action="">
            <input type="hidden" name="csrf" value="<?php echo $csrf_token; ?>">
            <input type="text" name="nombre" class="form-control" placeholder="Nombre Completo" required autofocus>
            <input type="email" name="email" class="form-control" placeholder="Correo electrónico" required>
            <input type="password" name="pwd" class="form-control" placeholder="Contraseña secreta" required minlength="5">
            <button type="submit" class="btn-submit">Registrarse y Entrar</button>
        </form>
        <div style="text-align:center; margin-top:20px; font-size:14px;">
            ¿Ya tienes una cuenta? <a href="login.php" style="color:#2563eb; text-decoration:none;"
       onmouseover="this.style.textDecoration='underline'"
       onmouseout="this.style.textDecoration='none'">Inicia Sesión</a>
        </div>

    </div>
    <script>
    // Validación del cliente con manipulación nativa del DOM (Requisito 3.1 y 2.1)
    document.addEventListener("DOMContentLoaded", function() {
        const form = document.querySelector("form");
        form.addEventListener("submit", function(event) {
            const pwd = document.querySelector("input[name='pwd']").value;
            if (pwd.length < 5) {
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
                errorDiv.innerText = "Validación JS Front-end: La contraseña es muy corta (mínimo 5 caracteres).";
            }
        });
    });
    </script>
</body>
</html>
