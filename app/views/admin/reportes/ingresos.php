<?php
// app/views/admin/reportes/ingresos.php
$ingresos = $data['ingresos'] ?? [];
$total = $data['total'] ?? 0;
?>
<?php $this->layout('layouts/admin', ['title' => 'Reporte de Ingresos', 'active' => 'reportes']) ?>
<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6"><h1>Reporte de Ingresos</h1></div>
        <div class="col-sm-6"><ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="/admin">Inicio</a></li>
          <li class="breadcrumb-item"><a href="/admin/reportes">Reportes</a></li>
          <li class="breadcrumb-item active">Ingresos</li>
        </ol></div>
      </div>
    </div>
  </section>
  <section class="content">
    <div class="container-fluid">
      <div class="card">
        <div class="card-header">
          <form class="form-inline" method="GET">
            <div class="form-group mr-2">
              <label class="mr-1">Desde:</label>
              <input type="date" name="desde" class="form-control form-control-sm" value="<?= htmlspecialchars($data['filtro_desde'] ?? date('Y-m-01')) ?>">
            </div>
            <div class="form-group mr-2">
              <label class="mr-1">Hasta:</label>
              <input type="date" name="hasta" class="form-control form-control-sm" value="<?= htmlspecialchars($data['filtro_hasta'] ?? date('Y-m-d')) ?>">
            </div>
            <div class="form-group mr-2">
              <select name="medio_pago" class="form-control form-control-sm">
                <option value="">Todos los medios</option>
                <?php foreach(['efectivo','transferencia','cheque','wompi','otro'] as $m): ?>
                  <option value="<?= $m ?>" <?= ($data['filtro_medio'] ?? '') === $m ? 'selected':'' ?>><?= ucfirst($m) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <button class="btn btn-sm btn-primary mr-2">Filtrar</button>
            <a href="/admin/reportes/ingresos/pdf?<?= http_build_query($_GET) ?>" class="btn btn-sm btn-danger"><i class="fas fa-file-pdf"></i> PDF</a>
          </form>
        </div>
        <div class="card-body">
          <div class="row mb-4">
            <div class="col-md-3">
              <div class="small-box bg-success">
                <div class="inner">
                  <h3>$ <?= number_format($total,0,',','.') ?></h3>
                  <p>Total Recaudado</p>
                </div>
                <div class="icon"><i class="fas fa-dollar-sign"></i></div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="small-box bg-info">
                <div class="inner">
                  <h3><?= count($ingresos) ?></h3>
                  <p>Transacciones</p>
                </div>
                <div class="icon"><i class="fas fa-receipt"></i></div>
              </div>
            </div>
          </div>
          <canvas id="chartIngresos" height="80"></canvas>
          <hr>
          <table class="table table-sm table-hover">
            <thead class="thead-light">
              <tr><th>Fecha</th><th>Estudiante</th><th>Concepto</th><th>Medio</th><th>Monto</th><th>Ref.</th></tr>
            </thead>
            <tbody>
              <?php foreach($ingresos as $i): ?>
              <tr>
                <td><?= $i['fecha_pago'] ?></td>
                <td><?= htmlspecialchars($i['est_nombres'].' '.$i['est_apellidos']) ?></td>
                <td><?= htmlspecialchars($i['concepto']) ?></td>
                <td><?= ucfirst($i['medio_pago']) ?></td>
                <td>$ <?= number_format($i['monto'],0,',','.') ?></td>
                <td><small><?= htmlspecialchars($i['referencia'] ?? '-') ?></small></td>
              </tr>
              <?php endforeach; ?>
              <?php if(!$ingresos): ?><tr><td colspan="6" class="text-center text-muted py-3">Sin ingresos en el período</td></tr><?php endif; ?>
            </tbody>
            <tfoot><tr class="font-weight-bold"><td colspan="4" class="text-right">Total:</td><td colspan="2">$ <?= number_format($total,0,',','.') ?></td></tr></tfoot>
          </table>
        </div>
      </div>
    </div>
  </section>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const labels = <?= json_encode(array_column($data['chart_data'] ?? [], 'fecha')) ?>;
const montos = <?= json_encode(array_column($data['chart_data'] ?? [], 'total')) ?>;
new Chart(document.getElementById('chartIngresos'), {
  type: 'bar',
  data: { labels, datasets: [{ label: 'Ingresos', data: montos, backgroundColor: 'rgba(40,167,69,0.7)' }] },
  options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
});
</script>