<?php
require_once __DIR__ . '/../src/db.php';
$db = getDB();
$code = $_GET['code'] ?? '';
$stmt = $db->prepare("SELECT a.*, c.nombre FROM apuestas a LEFT JOIN clientes c ON c.id=a.cliente_id WHERE a.voucher_code = ? LIMIT 1");
$stmt->execute([$code]);
$r = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$r) { echo "Voucher no encontrado"; exit; }
?>
<!doctype html><html><head><meta charset="utf-8"><title>Voucher</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head><body>
<div class="container mt-4">
  <div class="card p-4">
    <h3>Voucher - La Suerte</h3>
    <p><strong>Folio:</strong> <?=$r['voucher_code']?></p>
    <p><strong>Cliente:</strong> <?=htmlspecialchars($r['nombre'])?></p>
    <p><strong>Número:</strong> <?=htmlspecialchars($r['numero'])?></p>
    <p><strong>Monto:</strong> Q<?=number_format($r['monto'],2)?></p>
    <p><strong>Sorteo:</strong> <?=htmlspecialchars($r['sorteo'])?> - Evento <?=$r['evento_dia']?></p>
    <p><strong>Premio estimado:</strong> Q<?=number_format($r['premio_estimado'],2)?> <?= $r['es_cumple'] ? '<span class="badge bg-success">+10% Cumpleaños</span>' : '' ?></p>
    <p class="text-danger"><strong>Fecha límite de cobro:</strong> <?=htmlspecialchars($r['fecha_limite'])?></p>
    <p><strong>Atendido por:</strong> <?=htmlspecialchars($r['atendido_por'])?></p>
    <div class="mt-3">
      <button class="btn btn-primary" onclick="window.print()">Imprimir</button>
      <a class="btn btn-secondary" href="index.php">Nueva apuesta</a>
    </div>
  </div>
</div>
</body></html>

