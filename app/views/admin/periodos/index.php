<?php
// app/views/admin/periodos/index.php
$periodos = $data['periodos'] ?? [];
?>
<?php $this->layout('layouts/admin', ['title' => 'Períodos Académicos', 'active' => 'periodos']) ?>
<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6"><h1>Períodos Académicos</h1></div>
        <div class="col-sm-6"><ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="/admin">Inicio</a></li>
          <li class="breadcrumb-item active">Períodos</li>
        </ol></div>
      </div>
    </div>
  </section>
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-4">
          <div class="card card-primary">
            <div class="card-header"><h3 class="card-title">Nuevo Período</h3></div>
            <form action="/admin/periodos/store" method="POST">
              <div class="card-body">
                <div class="form-group">
                  <label>Nombre *</label>
                  <input type="text" name="nombre" class="form-control" placeholder="Ej: 1er Lapso 2025" required>
                </div>
                <div class="form-group">
                  <label>Año Escolar</label>
                  <input type="text" name="anio_escolar" class="form-control" value="<?= date('Y') ?>-<?= date('Y')+1 ?>">
                </div>
                <div class="form-group">
                  <label>Fecha de Inicio *</label>
                  <input type="date" name="fecha_inicio" class="form-control" required>
                </div>
                <div class="form-group">
                  <label>Fecha de Fin *</label>
                  <input type="date" name="fecha_fin" class="form-control" required>
                </div>
                <div class="form-check">
                  <input type="checkbox" name="activo" class="form-check-input" id="activo" value="1">
                  <label class="form-check-label" for="activo">Período Activo (abierto para calificaciones)</label>
                </div>
              </div>
              <div class="card-footer"><button type="submit" class="btn btn-primary btn-block"><i class="fas fa-plus"></i> Agregar Período</button></div>
            </form>
          </div>
        </div>
        <div class="col-md-8">
          <div class="card">
            <div class="card-header"><h3 class="card-title">Períodos Registrados</h3></div>
            <div class="card-body p-0">
              <table class="table table-hover table-sm">
                <thead class="thead-light">
                  <tr><th>Nombre</th><th>Año Escolar</th><th>Inicio</th><th>Fin</th><th>Estado</th><th>Acc.</th></tr>
                </thead>
                <tbody>
                  <?php foreach($periodos as $p): ?>
                  <tr>
                    <td><strong><?= htmlspecialchars($p['nombre']) ?></strong></td>
                    <td><?= htmlspecialchars($p['anio_escolar'] ?? '-') ?></td>
                    <td><?= $p['fecha_inicio'] ?></td>
                    <td><?= $p['fecha_fin'] ?></td>
                    <td>
                      <?php if($p['activo']): ?>
                        <span class="badge badge-success"><i class="fas fa-lock-open"></i> Abierto</span>
                      <?php else: ?>
                        <span class="badge badge-secondary"><i class="fas fa-lock"></i> Cerrado</span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <a href="/admin/periodos/edit/<?= $p['id'] ?>" class="btn btn-xs btn-warning"><i class="fas fa-edit"></i></a>
                      <?php if(!$p['activo']): ?>
                        <a href="/admin/periodos/activar/<?= $p['id'] ?>" class="btn btn-xs btn-success" onclick="return confirm('Activar este período? Esto cerrará el período activo actual.')"><i class="fas fa-lock-open"></i></a>
                      <?php else: ?>
                        <a href="/admin/periodos/cerrar/<?= $p['id'] ?>" class="btn btn-xs btn-secondary" onclick="return confirm('Cerrar este período para calificaciones?')"><i class="fas fa-lock"></i></a>
                      <?php endif; ?>
                      <a href="/admin/periodos/estructura/<?= $p['id'] ?>" class="btn btn-xs btn-info" title="Estructura de notas"><i class="fas fa-list"></i></a>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                  <?php if(!$periodos): ?><tr><td colspan="6" class="text-center text-muted py-3">Sin períodos registrados</td></tr><?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>