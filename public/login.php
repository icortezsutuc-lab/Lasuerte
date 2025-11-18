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
<!doctype html><html><head><meta charset="utf-8"><title>Login - La Suerte</title>
<link href="../assets/css/styles.css" rel="stylesheet">
</head><body>
<div class="login-box">
  <h2>La Suerte - Login</h2>
  <?php if($msg): ?><p class="error"><?=htmlspecialchars($msg)?></p><?php endif; ?>
  <form method="post">
    <label>Usuario</label><input name="username" required>
    <label>Contraseña</label><input name="password" type="password" required>
    <button type="submit">Ingresar</button>
  </form>
</div>
</body></html>

