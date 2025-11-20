<?php 
require_once __DIR__ . '/../src/auth.php'; 
require_login(); 
require_once __DIR__ . '/../src/db.php';
?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Registrar Apuesta</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
<div class="container mt-4">
  <h2>Registrar Apuesta</h2>

  <!-- 🔵 MENU COMPLETO -->
  <div class="mb-3">
      <a href="dashboard.php" class="btn btn-success btn-sm">Dashboard</a>
      <!-- <a href="reportes.php" class="btn btn-warning btn-sm">Reportes</a> -->
      <!-- <a href="registrar_apuesta.php" class="btn btn-primary btn-sm">Nueva Apuesta</a> -->
      <a href="logout.php" class="btn btn-danger btn-sm">Cerrar Sesión</a>
  </div>

  <form action="save_bet.php" method="POST" id="betForm" class="row g-3">

    <div class="col-md-6"><label>Nombre</label>
      <input name="nombre" class="form-control" required>
    </div>

    <div class="col-md-3"><label>Número (00-99)</label>
      <input name="numero" pattern="[0-9]{2}" class="form-control" required>
    </div>

    <div class="col-md-3"><label>Monto</label>
      <input name="monto" type="number" min="1" step="0.25" class="form-control" required>
    </div>

    <div class="col-md-4"><label>Sorteo</label>
      <select name="sorteo" class="form-select" id="sorteo" required>
        <option>La Santa</option>
        <option>La Rifa</option>
        <option>El Sorteo</option>
      </select>
    </div>

    <div class="col-md-4"><label>Evento (día)</label>
      <select name="sorteo_dia" id="sorteo_dia" class="form-select">
        <option value="1">1</option>
      </select>
    </div>

    <div class="col-md-4"><label>Fecha nacimiento (opcional)</label>
      <input type="date" name="bday" class="form-control">
    </div>

    <div class="col-md-6"><label>Atendido por</label>
      <input name="atendido_por" class="form-control" 
             value="<?=htmlspecialchars(current_user()['full_name'])?>" required>
    </div>

    <div class="col-12 text-end">
      <button class="btn btn-primary">Guardar Apuesta</button>
    </div>

    <div class="col-12 text-end mt-2">
      <button type="button" class="btn btn-secondary" onclick="imprimirVoucher()">Imprimir Voucher</button>
    </div>

  </form>
</div>

<script>
const selectSorteo = document.getElementById('sorteo');
const selectDia = document.getElementById('sorteo_dia');

selectSorteo.addEventListener('change', () => {
  let v = selectSorteo.value;
  let options = '';
  if (v === 'La Santa') options = '<option>1</option><option>2</option><option>3</option>';
  if (v === 'La Rifa') options = '<option>1</option>';
  if (v === 'El Sorteo') options = '<option>1</option><option>2</option>';
  selectDia.innerHTML = options;
});

// 🧾 IMPRIMIR VOUCHER MANUAL
function imprimirVoucher() {

    let nombre = document.querySelector('[name="nombre"]').value;
    let numero = document.querySelector('[name="numero"]').value;
    let monto = parseFloat(document.querySelector('[name="monto"]').value);
    let sorteo = document.querySelector('[name="sorteo"]').value;
    let dia = document.querySelector('[name="sorteo_dia"]').value;
    let atendido = document.querySelector('[name="atendido_por"]').value;
    let bday = document.querySelector('[name="bday"]').value;

    let premio = monto * 70;

    let ticket = `
        <div style="font-family: monospace; width: 260px;">
            <h3 style="text-align:center;">COMPROBANTE DE APUESTA</h3>
            <p><strong>Nombre:</strong> ${nombre}</p>
            <p><strong>Número:</strong> ${numero}</p>
            <p><strong>Monto Apostado:</strong> Q${monto.toFixed(2)}</p>
            <p><strong>Premio Posible:</strong> Q${premio.toFixed(2)}</p>
            <p><strong>Sorteo:</strong> ${sorteo}</p>
            <p><strong>Día evento:</strong> ${dia}</p>
            <p><strong>Fecha nacimiento:</strong> ${bday || "No aplica"}</p>
            <p><strong>Atendido por:</strong> ${atendido}</p>
            <hr>
            <p style="text-align:center;">Gracias por su compra</p>
        </div>
    `;

    let w = window.open("", "", "width=300,height=500");
    w.document.write(ticket);
    w.document.close();
    w.print();
}
</script>

</body>
</html>

