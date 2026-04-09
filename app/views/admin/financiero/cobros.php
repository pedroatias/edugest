<?php
// app/views/admin/financiero/cobros.php
$cobros = $data['cobros'] ?? [];
?>
<?php $this->layout('layouts/admin', ['title' => 'Gestión de Cobros', 'active' => 'financiero']) ?>
<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6"><h1>Gestión de Cobros</h1></div>
        <div class="col-sm-6"><ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="/admin">Inicio</a></li>
          <li class="breadcrumb-item active">Cobros</li>
        </ol></div>
      </div>
    </div>
  </section>
  <section class="content">
    <div class="container-fluid">
      <div class="card">
        <div class="card-header">
          <div class="row align-items-center">
            <div class="col-md-8">
              <form class="form-inline" method="GET">
                <select name="grado_id" class="form-control form-control-sm mr-2">
                  <option value="">Todos los grados</option>
                  <?php foreach($data['grados'] ?? [] as $g): ?>
                    <option value="<?= $g['id'] ?>" <?= ($data['fg'] ?? '') == $g['id'] ? 'selected' : '' ?>><?= htmlspecialchars($g['nombre']) ?></option>
                  <?php endforeach; ?>
                </select>
                <select name="concepto_id" class="form-control form-control-sm mr-2">
                  <option value="">Todos los conceptos</option>
                  <?php foreach($data['conceptos'] ?? [] as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= ($data['fc'] ?? '') == $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['nombre']) ?></option>
                  <?php endforeach; ?>
                </select>
                <button class="btn btn-sm btn-primary mr-2">Filtrar</button>
              </form>
            </div>
            <div class="col-md-4 text-right">
              <a href="/admin/financiero/cobros/generar" class="btn btn-warning btn-sm"><i class="fas fa-magic"></i> Generar Cobros Masivos</a>
            </div>
          </div>
        </div>
        <div class="card-body p-0">
          <table class="table table-hover table-sm">
            <thead class="thead-light">
              <tr><th>Estudiante</th><th>Grado</th><th>Concepto</th><th>Monto</th><th>Vencimiento</th><th>Estado</th><th>Acc.</th></tr>
            </thead>
            <tbody>
              <?php foreach($cobros as $c): ?>
              <tr>
                <td><?= htmlspecialchars($c['est_nombres'].' '.$c['est_apellidos']) ?></td>
                <td><?= htmlspecialchars($c['grado'].' '.$c['seccion']) ?></td>
                <td><?= htmlspecialchars($c['concepto']) ?></td>
                <td>$ <?= number_format($c['monto'],0,',','.') ?></td>
                <td><?= $c['fecha_vencimiento'] ?></td>
                <td>
                  <span class="badge badge-<?= ['pendiente'=>'warning','pagado'=>'success','vencido'=>'danger'][$c['estado']] ?? 'secondary' ?>">
                    <?= ucfirst($c['estado']) ?>
                  </span>
                </td>
                <td>
                  <a href="/admin/financiero/cobros/ver/<?= $c['id'] ?>" class="btn btn-xs btn-info"><i class="fas fa-eye"></i></a>
                </td>
              </tr>
              <?php endforeach; ?>
              <?php if(!$cobros): ?><tr><td colspan="7" class="text-center text-muted py-3">Sin cobros registrados</td></tr><?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </section>
</div>