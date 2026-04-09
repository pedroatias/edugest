<?php
// app/views/padre/pagos/confirmacion.php
$pago = $data['pago'] ?? [];
$exito = $data['exito'] ?? false;
?>
<?php $this->layout('layouts/padre', ['title' => 'Confirmación de Pago']) ?>
<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6"><h1>Confirmación de Pago</h1></div>
        <div class="col-sm-6"><ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="/padre">Inicio</a></li>
          <li class="breadcrumb-item"><a href="/padre/pagos">Pagos</a></li>
          <li class="breadcrumb-item active">Confirmación</li>
        </ol></div>
      </div>
    </div>
  </section>
  <section class="content">
    <div class="container-fluid">
      <div class="col-md-6 mx-auto">
        <div class="card text-center">
          <div class="card-body py-5">
            <?php if($exito): ?>
              <i class="fas fa-check-circle fa-5x text-success mb-3"></i>
              <h3 class="text-success">¡Pago Recibido!</h3>
              <p class="lead">Tu pago ha sido registrado y está siendo procesado.</p>
            <?php else: ?>
              <i class="fas fa-clock fa-5x text-warning mb-3"></i>
              <h3 class="text-warning">Pago en Proceso</h3>
              <p class="lead">Tu transacción está siendo validada por la institución.</p>
            <?php endif; ?>

            <?php if($pago): ?>
            <div class="card bg-light mt-4 text-left">
              <div class="card-body">
                <h6 class="card-subtitle mb-3 text-muted">Detalles del pago</h6>
                <table class="table table-sm mb-0">
                  <tr><th>Concepto:</th><td><?= htmlspecialchars($pago['concepto'] ?? '-') ?></td></tr>
                  <tr><th>Monto:</th><td>$ <?= number_format($pago['monto'] ?? 0, 0, ',', '.') ?></td></tr>
                  <tr><th>Referencia:</th><td><code><?= htmlspecialchars($pago['referencia_wompi'] ?? $pago['referencia'] ?? 'N/A') ?></code></td></tr>
                  <tr><th>Fecha:</th><td><?= date('d/m/Y H:i', strtotime($pago['created_at'] ?? 'now')) ?></td></tr>
                  <tr><th>Estado:</th><td>
                    <span class="badge badge-<?= ['pendiente'=>'secondary','en_proceso'=>'info','verificado'=>'success','rechazado'=>'danger'][$pago['estado'] ?? 'pendiente'] ?? 'secondary' ?>">
                      <?= ucfirst(str_replace('_',' ',$pago['estado'] ?? 'pendiente')) ?>
                    </span>
                  </td></tr>
                </table>
              </div>
            </div>
            <?php endif; ?>

            <div class="mt-4">
              <a href="/padre/pagos" class="btn btn-outline-primary mr-2"><i class="fas fa-receipt"></i> Ver mis pagos</a>
              <a href="/padre" class="btn btn-primary"><i class="fas fa-home"></i> Ir al inicio</a>
            </div>
            <p class="text-muted mt-4 small">Recibirás una notificación cuando tu pago sea verificado.<br>Ante cualquier duda, contacta a la institución.</p>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>