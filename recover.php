<?php
session_start();
require_once 'config/database.php';
require_once 'config/auth.php';

// Importar clases de PHPMailer en el espacio de nombres global
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Cargar archivos de PHPMailer manualmente
require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

if (isset($_SESSION['user_acc_id'])) {
    header("Location: modules/dashboard.php");
    exit();
}

$msg = '';
$enviado = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf'] ?? ''))
        die("Error CSRF");

    $email = trim($_POST['email']);
    if ($email) {
        $db = new DBConnection();
        $pdo = $db->connect();

        $stmt = $pdo->prepare("SELECT id_usuario FROM usuarios WHERE correo = ?");
        $stmt->execute([$email]);
        if ($user = $stmt->fetch()) {
            $token = bin2hex(random_bytes(32));
            $exp = date('Y-m-d H:i:s', strtotime('+1 hour'));
            $pdo->prepare("UPDATE usuarios SET reset_token=?, reset_expires=? WHERE id_usuario=?")->execute([$token, $exp, $user['id_usuario']]);

            // Lógica de PHPMailer
            $mail = new PHPMailer(true);
            try {
                // Configuración del Servidor SMTP (Gmail)
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;

                // IMPORTANTE: El usuario debe poner sus credenciales reales aquí
                $mail->Username = '[tu-correo]';
                $mail->Password = '[tu-password_app]';

                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;
                $mail->CharSet = 'UTF-8';

                // Remitente y Destinatario
                $mail->setFrom('sistema@biblioteca.local', 'Sistema de Biblioteca');
                $mail->addAddress($email);

                // Contenido del Correo
                $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ? "https" : "http";
                $base_dir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
                $reset_link = $protocol . "://" . $_SERVER['HTTP_HOST'] . $base_dir . "/reset.php?t=$token";

                $mail->isHTML(true);
                $mail->Subject = 'Recuperación de Contraseña SIS-BIBLIOTECA';
                $mail->Body = "Hola,<br><br>Has solicitado restablecer tu contraseña. Haz clic en el siguiente enlace para crear una nueva:<br><br>
                                  <a href='$reset_link' style='padding:10px 15px; background:#2563eb; color:#fff; text-decoration:none; border-radius:5px;'>Restablecer Contraseña</a>
                                  <br><br>Si no solicitaste este cambio, simplemente ignora este mensaje.";

                $mail->send();
                $msg = "<div class='alert alert-success'>Hemos enviado un correo de recuperación a $email. (Revisa la bandeja de SPAM)</div>";
                $enviado = true;
            }
            catch (Exception $e) {
                // En este bloque capturamos cualquier error de envío para que el alumno lo depure
                $msg = "<div class='alert alert-error'>No se pudo enviar el correo de verdad. Configura tu Correo y Contraseña dentro del código en la línea 45 y 46.<br><br>Error: {$mail->ErrorInfo}</div>";
            }

        }
        else {
            // Confirmación genérica por seguridad
            $msg = "<div class='alert alert-success'>Si el correo existe en nuestra base de datos, recibirás un enlace de recuperación.</div>";
            $enviado = true;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recuperar Contraseña | Biblioteca</title>
    <link rel="stylesheet" href="assets/recover.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <a href="index.php" style="position:absolute; top:20px; left:20px; color:white; text-decoration:none; font-size:14px; font-weight:600; background:rgba(0,0,0,0.5); padding:10px 18px; border-radius:30px; backdrop-filter:blur(4px); border:1px solid rgba(255,255,255,0.2); transition:all 0.3s;" onmouseover="this.style.background='rgba(0,0,0,0.8)'" onmouseout="this.style.background='rgba(0,0,0,0.5)'">
        <i class="fa fa-home"></i> Volver al Inicio
    </a>
    <div class="login-box">
        <h2 style="text-align:center;">Recuperar Acceso</h2>
        <?php echo $msg; ?>
        <?php if (!$enviado): ?>
        <form method="POST">
            <input type="hidden" name="csrf" value="<?php echo generate_csrf(); ?>">
            <p style="font-size:14px; color:#666; margin-bottom:15px;">Ingresa tu correo asociado a la plataforma:</p>
            <input type="email" name="email" class="form-control" required>
            <button type="submit" class="btn-submit">Enviar Enlace a Mi Correo</button>
        </form>
        <?php
endif; ?>
        <div style="text-align:center; margin-top:20px;">
            <a href="login.php" style="color:#2563eb; text-decoration:none; font-size:14px;">Volver al Login</a>
        </div>

    </div>
</body>
</html>

