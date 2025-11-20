<?php
require_once __DIR__ . "/../src/db.php";
require_once __DIR__ . "/../src/functions.php";
require_once __DIR__ . "/../src/auth.php";

require_login();
date_default_timezone_set('America/Guatemala');

$db = getDB();

// ======= RECIBIR DATOS =======
$nombre = trim($_POST['nombre'] ?? '');
$numero = trim($_POST['numero'] ?? '');
$monto = floatval($_POST['monto'] ?? 0);
$sorteo = $_POST['sorteo'] ?? '';
$sorteo_dia = intval($_POST['sorteo_dia'] ?? 1);
$atendido = trim($_POST['atendido_por'] ?? '');
$bday = $_POST['bday'] ?? null;

$errors = [];
if ($nombre === '') $errors[] = 'Nombre requerido';
if (!preg_match('/^[0-9]{2}$/', $numero)) $errors[] = 'Número inválido (2 dígitos)';
if ($monto <= 0) $errors[] = 'Monto inválido';
if ($sorteo === '') $errors[] = 'Seleccione un sorteo';

if ($errors) {
    echo '<p>Errores:</p><ul><li>'.implode('</li><li>', $errors).'</li></ul>';
    exit;
}

// ======= CLIENTE =======
$cliente = findClienteByName($nombre);

if (!$cliente) {
    $cliente_id = crearCliente($nombre, null, $bday);
} else {
    $cliente_id = $cliente['id'];
}

// ======= CALCULAR PREMIO =======
$premioBase = calcularPremio($monto, $sorteo);  // función personalizada
$isBirthday = esCumple($bday, date('Y-m-d'));
$bono = $isBirthday ? $premioBase * 0.10 : 0;
$premioTotal = $premioBase + $bono;

// ======= FECHA LIMITE =======
$fechaLimite = sumarDiasHabiles(date('Y-m-d'), 5);

// ======= GENERAR VOUCHER =======
$voucher = generarVoucher();

// ======= INSERTAR APUESTA =======
$stmt = $db->prepare("
    INSERT INTO apuestas 
    (cliente_id, numero, monto, sorteo, evento_dia, premio_estimado, es_cumple, fecha_limite, atendido_por, voucher_code)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");

$stmt->execute([
    $cliente_id,
    $numero,
    $monto,
    $sorteo,
    $sorteo_dia,
    $premioTotal,
    $isBirthday ? 1 : 0,
    $fechaLimite,
    $atendido,
    $voucher
]);

// ======= REDIRIGIR A VOUCHER =======
// SI QUIERES IR DIRECTO A IMPRIMIR
header("Location: voucher.php?code=" . urlencode($voucher));
exit;
?>
