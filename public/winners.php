<?php
require_once __DIR__ . '/../src/auth.php';
require_login();
require_once __DIR__ . '/../src/db.php';
$db = getDB();

$sorteo = $_GET['sorteo'] ?? null;
$event_date = $_GET['event_date'] ?? date('Y-m-d');
$event_number = intval($_GET['event_number'] ?? 1);

$winners = [];
if ($sorteo) {
    $stmt = $db->prepare("SELECT winning_number FROM raffle_results WHERE sorteo = ? AND event_date = ? AND event_number = ? LIMIT 1");
    $stmt->execute([$sorteo, $event_date, $event_number]);
    $win = $stmt->fetchColumn();
    if ($win) {
        $stmt2 = $db->prepare("SELECT a.*, c.nombre FROM apuestas a LEFT JOIN clientes c ON c.id=a.cliente_id WHERE a.sorteo = ? AND a.numero = ?");
        $stmt2->execute([$sorteo, $win]);
        $winners = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
<!doctype html><html><head><meta charset="utf-8"><title>Ganadores</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head><body class="p-4">
<?php include 'partials/navbar.php'; ?>
<h3>Listado de Ganadores</h3>

<form method="get" class="row g-3">
  <div class="col-md-3">
    <select name="sorteo" class="form-select">
      <option value="">Seleccione...</option>
      <option value="La Santa">La Santa</option>
      <option value="La Rifa">La Rifa</option>
      <option value="El Sorteo">El Sorteo</option>
    </select>
  </div>
  <div class="col-md-3"><input type="date" name="event_date" class="form-control" value="<?=$event_date?>"></div>
  <div class="col-md-2"><input type="number" name="event_number" class="form-control" value="<?=$event_number?>"></div>
  <div class="col-md-2"><button class="btn btn-primary">Buscar</button></div>
</form>

<?php if ($sorteo && empty($winners)) : ?>
  <div class="alert alert-warning mt-3">Ganador desierto o resultado no publicado.</div>
<?php endif; ?>

<?php if (!empty($winners)) : ?>
<table class="table mt-3">
<thead><tr><th>Folio</th><th>Cliente</th><th>Número</th><th>Monto</th><th>Premio estimado</th></tr></thead>
<tbody>
<?php foreach($winners as $w): ?>
  <tr>
    <td><?=htmlspecialchars($w['voucher_code'])?></td>
    <td><?=htmlspecialchars($w['nombre'])?></td>
    <td><?=htmlspecialchars($w['numero'])?></td>
    <td>Q<?=number_format($w['monto'],2)?></td>
    <td>Q<?=number_format($w['premio_estimado'],2)?></td>
  </tr>
<?php endforeach; ?>
</tbody>
</table>
<?php endif; ?>

</body></html>
