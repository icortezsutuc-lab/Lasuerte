<?php
require_once __DIR__ . '/../src/auth.php';
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (login($_POST['username'] ?? '', $_POST['password'] ?? '')) {
        header('Location: dashboard.php');
        exit;
    } else {
        $msg = 'Credenciales inválidas';
    }
}
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Login - La Suerte</title>

<style>
    body {
        margin: 0;
        height: 100vh;
        background: linear-gradient(135deg, #1b1b1b, #4b0082, #8a2be2);
        display: flex;
        justify-content: center;
        align-items: center;
        font-family: "Segoe UI", sans-serif;
        color: #fff;
    }

    .footer-text {
        position: absolute;
        bottom: 20px;
        text-align: center;
        font-size: 18px;
        font-weight: bold;
        color: #ffd700;
        text-shadow: 0 0 10px #000;
    }

    .login-box {
        width: 380px;
        padding: 40px;
        border-radius: 20px;
        background: rgba(255, 255, 255, 0.12);
        backdrop-filter: blur(12px);
        box-shadow: 0 0 20px rgba(0,0,0,0.4);
        text-align: center;
        animation: fadeIn 1s ease;
    }

    .login-box h2 {
        margin-bottom: 25px;
        font-size: 28px;
        color: #ffd700;
        text-shadow: 0 0 10px #000;
    }

    .login-box label {
        display: block;
        text-align: left;
        margin: 10px 0 5px;
        font-weight: bold;
    }

    .login-box input {
        width: 100%;
        padding: 12px;
        border-radius: 10px;
        border: none;
        margin-bottom: 10px;
        background: rgba(255,255,255,0.8);
        font-size: 16px;
    }

    .login-box button {
        width: 100%;
        padding: 12px;
        border-radius: 10px;
        border: none;
        background: #ffd700;
        font-size: 18px;
        cursor: pointer;
        font-weight: bold;
        transition: 0.3s;
    }

    .login-box button:hover {
        background: #ffcc00;
        transform: scale(1.03);
    }

    .error {
        background: rgba(255,0,0,0.7);
        padding: 10px;
        border-radius: 8px;
        margin-bottom: 10px;
        font-weight: bold;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to   { opacity: 1; transform: translateY(0); }
    }
</style>

</head>
<body>

<div class="login-box">
    <h2>🔐 La Suerte - Inicio de Sesión</h2>

    <?php if($msg): ?>
        <p class="error"><?=htmlspecialchars($msg)?></p>
    <?php endif; ?>

    <form method="post">
        <label>Usuario</label>
        <input name="username" required>

        <label>Contraseña</label>
        <input name="password" type="password" required>

        <button type="submit">Ingresar</button>
    </form>
</div>

<div class="footer-text">
    ⭐ Realizado Por: <span style="color:#fff">Ismael Enrique Cortez Sutuc</span> <br>
    📌 Proyecto PrePrivado
</div>

</body>
</html>


