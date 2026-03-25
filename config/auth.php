<?php
session_start();

function generate_csrf() {
    if (empty($_SESSION['csrf_string'])) {
        $_SESSION['csrf_string'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_string'];
}

function verify_csrf($token) {
    if (isset($_SESSION['csrf_string']) && hash_equals($_SESSION['csrf_string'], $token)) {
        return true;
    }
    return false;
}

function check_auth() {
    if (!isset($_SESSION['user_acc_id'])) {
        header("Location: ../login.php");
        exit();
    }
}

function require_admin() {
    check_auth();
    if ($_SESSION['user_role'] != 1) {
        die("Acceso Denegado: Se requieren permisos de administrador.");
    }
}
?>
