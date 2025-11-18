<?php
require_once __DIR__ . "/../src/db.php";
require_once __DIR__ . "/../src/functions.php";

date_default_timezone_set('America/Guatemala'); // ← Asegura la hora local


$db = getDB();

$nombre = trim($_POST['nombre'] ?? '');
$numero = trim($_POST['numero'] ?? '');
$monto = floatval($_POST['monto'] ?? 0);
$sorteo = $_POST['sorteo'] ?? '';
$sorteo_dia = intval($_POST['sorteo_dia'] ?? 1);
$atendido = trim($_POST['atendido_por'] ?? '');
$bday = $_POST['bday'] ?? null;

$fecha = date("Y-m-d H:i:s");

$errors = [];
if ($nombre === '') $errors[] = 'Nombre requerido';
if (!preg_match('/^[0-9]{2}$/', $numero)) $errors[] = 'Número inválido (2 dígitos)';
if ($monto <= 0) $errors[] = 'Monto inválido';
if ($sorteo === '') $errors[] = 'Seleccione sorteo';

if ($errors) {
    echo '<p>Errores:</p><ul><li>'.implode('</li><li>', $errors).'</li></ul>';
    echo '<p><a href="../public/index.php">Volver</a></p>';
    exit;
}

// registrar cliente si no existe
$cliente = findClienteByName($nombre);
if (!$cliente) {
    $cliente_id = crearCliente($nombre, null, $bday);
} else {
    $cliente_id = $cliente['id'];
}

// calcular premio
$premioBase = calcularPremio($monto, $sorteo);
$cumple = esCumple($bday, date('Y-m-d'));
$bono = $cumple ? $premioBase * 0.10 : 0;
$premioTotal = $premioBase + $bono;

// fecha limite 5 dias habiles desde hoy (o evento date si lo manejas)
$fechaLimite = sumarDiasHabiles(date('Y-m-d'), 5);

// voucher
$voucher = generarVoucher();

// insertar apuesta
$stmt = $db->prepare("INSERT INTO apuestas (cliente_id, numero, monto, sorteo, evento_dia, premio_estimado, es_cumple, fecha_limite, atendido_por, voucher_code) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->execute([$cliente_id, $numero, $monto, $sorteo, $sorteo_dia, $premioTotal, $cumple ? 1 : 0, $fechaLimite, $atendido, $voucher]);

$apuesta_id = $db->lastInsertId();

// redirigir a voucher imprimible
header('Location: ../public/voucher.php?code=' . urlencode($voucher));

header("Location: /suerte/public/dashboard.php");
exit;
