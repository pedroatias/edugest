<?php
// app/views/admin/pagos/index.php
$pagos = $data['pagos'] ?? [];
$periodos = $data['periodos'] ?? [];
?>
<?php $this->layout('layouts/admin', ['title' => 'Gestión de Pagos', 'active' => 'pagos']) ?>
<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6"><h1>Gestión de Pagos</h1></div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="/admin">Inicio</a></li>
            <li class="breadcrumb-item active">Pagos</li>
          </ol>
        </div>
      </div>
    </div>
  </section>
  <section class="content">
    <div class="container-fluid">
      <?php if(!empty($data['flash'])): ?>
        <div class="alert alert-<?= $data['flash']['type'] ?> alert-dismissible">
          <button type="button" class="close" data-dismiss="alert">&times;</button>
          <?= htmlspecialchars($data['flash']['msg']) ?>
        </div>
      <?php endif; ?>
      <div class="card">
        <div class="card-header">
          <div class="row align-items-center">
            <div class="col-md-6">
              <form class="form-inline" method="GET" action="/admin/pagos">
                <select name="estado" class="form-control form-control-sm mr-2">
                  <option value="">Todos los estados</option>
                  <?php foreach(['pendiente','en_proceso','verificado','rechazado'] as $e): ?>
                    <option value="<?= $e ?>" <?= ($data['filtro_estado'] ?? '') === $e ? 'selected' : '' ?>><?= ucfirst(str_replace('_',' ',$e)) ?></option>
                  <?php endforeach; ?>
                </select>
                <input type="text" name="q" class="form-control form-control-sm mr-2" placeholder="Buscar estudiante..." value="<?= htmlspecialchars($data['filtro_q'] ?? '') ?>">
                <button class="btn btn-sm btn-primary">Filtrar</button>
              </form>
            </div>
            <div class="col-md-6 text-right">
              <a href="/admin/pagos/nuevo" class="btn btn-success btn-sm"><i class="fas fa-plus"></i> Registrar Pago Manual</a>
            </div>
          </div>
        </div>
        <div class="card-body p-0">
          <table class="table table-hover table-sm">
            <thead class="thead-light">
              <tr>
                <th>#</th><th>Estudiante</th><th>Grado/Secc.</th><th>Concepto</th><th>Monto</th><th>Estado</th><th>Fecha</th><th>Ref. Wompi</th><th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach($pagos as $p): ?>
              <tr>
                <td><?= $p['id'] ?></td>
                <td><?= htmlspecialchars($p['est_nombres'].' '.$p['est_apellidos']) ?></td>
                <td><?= htmlspecialchars($p['grado'].' '.$p['seccion']) ?></td>
                <td><?= htmlspecialchars($p['concepto']) ?></td>
                <td>$ <?= number_format($p['monto'], 0, ',', '.') ?></td>
                <td>
                  <span class="badge badge-<?= ['pendiente'=>'secondary','en_proceso'=>'info','verificado'=>'success','rechazado'=>'danger'][$p['estado']] ?? 'light' ?>">
                    <?= ucfirst(str_replace('_',' ',$p['estado'])) ?>
                  </span>
                </td>
                <td><?= $p['fecha_pago'] ?? $p['created_at'] ?></td>
                <td><small><?= htmlspecialchars($p['referencia_wompi'] ?? '-') ?></small></td>
                <td>
                  <?php if($p['estado'] === 'en_proceso'): ?>
                    <a href="/admin/pagos/verificar/<?= $p['id'] ?>" class="btn btn-xs btn-success" onclick="return confirm('Verificar este pago?')"><i class="fas fa-check"></i></a>
                    <a href="/admin/pagos/rechazar/<?= $p['id'] ?>" class="btn btn-xs btn-danger" onclick="return confirm('Rechazar este pago?')"><i class="fas fa-times"></i></a>
                  <?php endif; ?>
                  <a href="/admin/pagos/ver/<?= $p['id'] ?>" class="btn btn-xs btn-info"><i class="fas fa-eye"></i></a>
                </td>
              </tr>
              <?php endforeach; ?>
              <?php if(!$pagos): ?>
                <tr><td colspan="9" class="text-center text-muted py-4">No hay pagos registrados</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
        <?php if(!empty($data['pagination'])): ?>
          <div class="card-footer"><?= $data['pagination'] ?></div>
        <?php endif; ?>
      </div>
    </div>
  </section>
</div>