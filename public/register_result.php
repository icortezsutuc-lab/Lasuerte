<?php
require_once __DIR__ . '/../src/auth.php';
require_login();
require_once __DIR__ . '/../src/db.php';

$db = getDB();
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sorteo = $_POST['sorteo'];
    $event_date = $_POST['event_date'];
    $event_number = intval($_POST['event_number']);
    $winning_number = $_POST['winning_number'];

    $stmt = $db->prepare("INSERT INTO raffle_results (sorteo, event_date, event_number, winning_number) VALUES (?, ?, ?, ?)");
    $stmt->execute([$sorteo, $event_date, $event_number, $winning_number]);
    $msg = "Resultado publicado.";
}
?>
<!doctype html><html><head><meta charset="utf-8"><title>Registrar Resultado</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head><body class="p-4">
<?php include 'partials/navbar.php'; ?>
<h3>Registrar Resultado</h3>
<?php if($msg) echo "<div class='alert alert-success'>$msg</div>"; ?>
<form method="post" class="row g-3">
  <div class="col-md-3">
    <select name="sorteo" class="form-select">
      <option>La Santa</option><option>La Rifa</option><option>El Sorteo</option>
    </select>
  </div>
  <div class="col-md-3"><input type="date" name="event_date" class="form-control" value="<?=date('Y-m-d')?>"></div>
  <div class="col-md-2"><input type="number" name="event_number" class="form-control" value="1" min="1"></div>
  <div class="col-md-2"><input type="text" name="winning_number" class="form-control" placeholder="00"></div>
  <div class="col-md-2"><button class="btn btn-primary">Publicar</button></div>
</form>
</body></html>
