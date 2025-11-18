<?php
require_once __DIR__ . '/../src/auth.php';
require_login();
require_once __DIR__ . '/../src/db.php';

$db = getDB();

// filtros
$start = $_GET['start'] ?? null;
$end = $_GET['end'] ?? null;
$sorteo = $_GET['sorteo'] ?? null;

$where = [];
$params = [];
if ($start && $end) { $where[] = "date(fecha_registro) BETWEEN ? AND ?"; $params[] = $start; $params[] = $end; }
if ($sorteo) { $where[] = "sorteo = ?"; $params[] = $sorteo; }

$sql = "SELECT a.*, c.nombre FROM apuestas a LEFT JOIN clientes c ON c.id=a.cliente_id";
if ($where) $sql .= " WHERE " . implode(' AND ', $where);
$sql .= " ORDER BY fecha_registro DESC";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// exportar CSV
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=apuestas.csv');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['ID','Cliente','Numero','Monto','Sorteo','PremioEstimado','Fecha']);
    foreach ($rows as $r) {
        fputcsv($out, [$r['id'], $r['nombre'], $r['numero'], $r['monto'], $r['sorteo'], $r['premio_estimado'], $r['fecha_registro']]);
    }
    exit;
}
?>
<!doctype html><html><head><meta charset="utf-8"><title>Reportes</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head><body class="p-4">
<?php include 'partials/navbar.php'; ?>
<h3>Reportes</h3>
<form class="row g-3" method="get">
  <div class="col-md-3"><input type="date" name="start" class="form-control" value="<?=htmlspecialchars($start)?>"></div>
  <div class="col-md-3"><input type="date" name="end" class="form-control" value="<?=htmlspecialchars($end)?>"></div>
  <div class="col-md-3">
    <select name="sorteo" class="form-select">
      <option value="">--Todos--</option>
      <option <?= $sorteo==='La Santa'?'selected':''?>>La Santa</option>
      <option <?= $sorteo==='La Rifa'?'selected':''?>>La Rifa</option>
      <option <?= $sorteo==='El Sorteo'?'selected':''?>>El Sorteo</option>
    </select>
  </div>
  <div class="col-md-3">
    <button class="btn btn-primary">Filtrar</button>
    <a class="btn btn-success" href="?<?=http_build_query(array_merge($_GET,['export'=>'csv']))?>">Exportar CSV</a>
  </div>
</form>

<table class="table table-striped mt-3">
<thead><tr><th>ID</th><th>Cliente</th><th>Número</th><th>Monto</th><th>Sorteo</th><th>Premio</th><th>Fecha</th></tr></thead>
<tbody>
<?php foreach($rows as $r): ?>
<tr>
  <td><?=$r['id']?></td>
  <td><?=htmlspecialchars($r['nombre'])?></td>
  <td><?=$r['numero']?></td>
  <td>Q<?=number_format($r['monto'],2)?></td>
  <td><?=$r['sorteo']?></td>
  <td>Q<?=number_format($r['premio_estimado'],2)?></td>
  <td><?=$r['fecha_registro']?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</body></html>
