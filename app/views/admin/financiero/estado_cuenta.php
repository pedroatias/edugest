<?php
// app/views/admin/financiero/estado_cuenta.php
$est = $data['estudiante'] ?? [];
$cobros = $data['cobros'] ?? [];
$saldo = $data['saldo'] ?? 0;
?>
<?php $this->layout('layouts/admin', ['title' => 'Estado de Cuenta', 'active' => 'financiero']) ?>
<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6"><h1>Estado de Cuenta por Estudiante</h1></div>
        <div class="col-sm-6"><ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="/admin">Inicio</a></li>
          <li class="breadcrumb-item active">Estado de Cuenta</li>
        </ol></div>
      </div>
    </div>
  </section>
  <section class="content">
    <div class="container-fluid">
      <div class="card">
        <div class="card-header">
          <form class="form-inline" method="GET">
            <label class="mr-2">Buscar Estudiante:</label>
            <select name="estudiante_id" class="form-control form-control-sm mr-2 select2" style="min-width:300px">
              <option value="">-- Seleccione --</option>
              <?php foreach($data['estudiantes'] ?? [] as $e): ?>
                <option value="<?= $e['id'] ?>" <?= ($data['estudiante_id'] ?? '') == $e['id'] ? 'selected' : '' ?>>
                  <?= htmlspecialchars($e['nombres'].' '.$e['apellidos'].' - '.$e['grado'].' '.$e['seccion']) ?>
                </option>
              <?php endforeach; ?>
            </select>
            <button class="btn btn-sm btn-primary">Ver Estado</button>
            <?php if($est): ?>
              <a href="/admin/financiero/estado_cuenta/pdf/<?= $est['id'] ?>" class="btn btn-sm btn-danger ml-2"><i class="fas fa-file-pdf"></i> PDF</a>
            <?php endif; ?>
          </form>
        </div>
        <?php if($est): ?>
        <div class="card-body">
          <div class="row mb-3">
            <div class="col-md-6">
              <h5><?= htmlspecialchars($est['nombres'].' '.$est['apellidos']) ?></h5>
              <p class="text-muted mb-1"><?= htmlspecialchars($est['grado'].' - '.$est['seccion']) ?></p>
              <p class="text-muted mb-0">Representante: <?= htmlspecialchars($est['rep_nombre'] ?? '-') ?></p>
            </div>
            <div class="col-md-6 text-right">
              <div class="info-box bg-<?= $saldo > 0 ? 'danger' : 'success' ?> d-inline-block" style="min-width:200px">
                <span class="info-box-icon"><i class="fas fa-<?= $saldo > 0 ? 'exclamation-triangle' : 'check-circle' ?>"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text">Saldo <?= $saldo > 0 ? 'Pendiente' : 'al Dia' ?></span>
                  <span class="info-box-number">$ <?= number_format(abs($saldo),0,',','.') ?></span>
                </div>
              </div>
            </div>
          </div>
          <table class="table table-sm table-bordered">
            <thead class="thead-light">
              <tr><th>Concepto</th><th>Vencimiento</th><th>Monto</th><th>Pagado</th><th>Saldo</th><th>Estado</th></tr>
            </thead>
            <tbody>
              <?php foreach($cobros as $c):
                $pagado = $c['monto_pagado'] ?? 0;
                $pendiente = $c['monto'] - $pagado;
              ?>
              <tr class="<?= $c['estado'] === 'vencido' ? 'table-danger' : ($c['estado'] === 'pagado' ? 'table-success' : '') ?>">
                <td><?= htmlspecialchars($c['concepto']) ?></td>
                <td><?= $c['fecha_vencimiento'] ?></td>
                <td>$ <?= number_format($c['monto'],0,',','.') ?></td>
                <td>$ <?= number_format($pagado,0,',','.') ?></td>
                <td>$ <?= number_format($pendiente,0,',','.') ?></td>
                <td><span class="badge badge-<?= ['pendiente'=>'warning','pagado'=>'success','vencido'=>'danger'][$c['estado']] ?? 'secondary' ?>"><?= ucfirst($c['estado']) ?></span></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
            <tfoot>
              <tr class="font-weight-bold">
                <td colspan="4" class="text-right">Total Pendiente:</td>
                <td colspan="2">$ <?= number_format($saldo,0,',','.') ?></td>
              </tr>
            </tfoot>
          </table>
        </div>
        <?php else: ?>
        <div class="card-body text-center text-muted py-5">
          <i class="fas fa-search fa-3x mb-3 d-block"></i>
          Seleccione un estudiante para ver su estado de cuenta
        </div>
        <?php endif; ?>
      </div>
    </div>
  </section>
</div>