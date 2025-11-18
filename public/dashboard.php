<?php
require_once __DIR__ . '/../src/auth.php';
require_login();
require_once __DIR__ . '/../src/functions.php';

$db = getDB();
// totales
$totHoy = $db->prepare("SELECT SUM(monto) FROM apuestas WHERE date(fecha_registro) = date('now')");
$totHoy->execute(); $totalHoy = $totHoy->fetchColumn() ?: 0;

$totalApuestas = $db->query("SELECT COUNT(*) FROM apuestas")->fetchColumn();

// totales por sorteo
$stmt = $db->query("SELECT sorteo, SUM(monto) as total FROM apuestas GROUP BY sorteo");
$porSorteo = $stmt->fetchAll(PDO::FETCH_ASSOC);

// top numeros
$stmt2 = $db->query("SELECT numero, COUNT(*) as veces FROM apuestas GROUP BY numero ORDER BY veces DESC LIMIT 10");
$topNumeros = $stmt2->fetchAll(PDO::FETCH_ASSOC);

$user = current_user();
?>
<!doctype html><html><head>
<meta charset="utf-8"><title>Dashboard - La Suerte</title>
<link href="../assets/css/styles.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head><body>
<?php include __DIR__.'/partials/navbar.php'; ?>
<div class="container">
  <h2>Dashboard</h2>
  <div class="cards">
    <div class="card">Total hoy: Q<?=number_format($totalHoy,2)?></div>
    <div class="card">Apuestas totales: <?=$totalApuestas?></div>
    <div class="card">Usuario: <?=htmlspecialchars($user['full_name'])?></div>
  </div>

  <h3>Recaudación por sorteo</h3>
  <canvas id="chartSorteos" width="600" height="250"></canvas>

  <h3>Top números</h3>
  <canvas id="chartTop" width="600" height="250"></canvas>
</div>

<script>
const porSorteo = <?= json_encode($porSorteo) ?>;
const topNumeros = <?= json_encode($topNumeros) ?>;

const labels1 = porSorteo.map(x => x.sorteo);
const data1 = porSorteo.map(x => parseFloat(x.total));

const labels2 = topNumeros.map(x => x.numero);
const data2 = topNumeros.map(x => parseInt(x.veces));

new Chart(document.getElementById('chartSorteos').getContext('2d'), {
  type: 'bar',
  data: { labels: labels1, datasets: [{ label: 'Recaudación', data: data1 }] }
});

new Chart(document.getElementById('chartTop').getContext('2d'), {
  type: 'bar',
  data: { labels: labels2, datasets: [{ label: 'Veces apostado', data: data2 }] }
});
</script>
</body></html>
